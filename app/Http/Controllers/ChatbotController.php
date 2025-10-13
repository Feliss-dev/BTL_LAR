<?php

namespace App\Http\Controllers;

use App\Models\ChatHistory;
use App\Services\GeminiChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    private GeminiChatService $geminiService;

    public function __construct(GeminiChatService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function chat(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
                'session_id' => 'nullable|string'
            ]);

            $sessionId = $request->session_id ?: Str::uuid()->toString();
            $userId = Auth::check() ? Auth::user()->id : null;

            Log::info('Chatbot request received', [
                'message' => $request->message,
                'session_id' => $sessionId,
                'user_id' => $userId
            ]);

            $result = $this->geminiService->generateResponse(
                $request->message,
                $sessionId,
                $userId
            );

            return response()->json($result);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Chatbot controller error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra. Vui lòng thử lại.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function streamChat(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
                'session_id' => 'nullable|string',
                'force_search' => 'nullable' // allow boolean-like
            ]);

            $sessionId = $request->session_id ?: Str::uuid()->toString();
            $userId = Auth::check() ? Auth::user()->id : null;

            // Add user message first (for immediate display)
            $chatRecord = ChatHistory::create([
                'session_id' => $sessionId,
                'user_id' => $userId,
                'user_message' => $request->message,
                'bot_response' => '', // Will be updated later
                'suggested_products' => []
            ]);

            $forceSearch = filter_var($request->force_search, FILTER_VALIDATE_BOOLEAN);

            // If client requested force_search (quick-prompt), return server-side search immediately
            if ($forceSearch) {
                $products = $this->geminiService->searchProductsByKeywords($request->message, 4);

                // Update chat record
                $cleanText = 'Mình tìm được một vài sản phẩm phù hợp với yêu cầu của bạn.';
                $chatRecord->update([
                    'bot_response' => $cleanText,
                    'suggested_products' => $products
                ]);

                $response = response()->stream(function () use ($cleanText, $products, $sessionId) {
                    // send content
                    echo "data: " . json_encode([
                        'type' => 'content',
                        'text' => $cleanText,
                        'session_id' => $sessionId
                    ]) . "\n\n";
                    ob_flush();
                    flush();

                    if (!empty($products)) {
                        echo "data: " . json_encode([
                            'type' => 'products',
                            'products' => $products,
                            'session_id' => $sessionId
                        ]) . "\n\n";
                        ob_flush();
                        flush();
                    }

                    echo "data: " . json_encode([
                        'type' => 'done',
                        'session_id' => $sessionId
                    ]) . "\n\n";
                    ob_flush();
                    flush();
                }, 200, [
                    'Content-Type' => 'text/event-stream',
                    'Cache-Control' => 'no-cache',
                    'Connection' => 'keep-alive',
                    'X-Accel-Buffering' => 'no',
                ]);

                return $response;
            }

            // Normal streaming path (unchanged)
            $response = response()->stream(function () use ($request, $sessionId, $userId) {
                // Try open SSE stream
                $stream = $this->geminiService->streamResponse(
                    $request->message,
                    $sessionId,
                    $userId
                );

                // Fallback: if stream couldn't be opened (false), call synchronous generateResponse and send single SSE events
                if ($stream === false) {
                    Log::warning('Gemini stream unavailable, using synchronous fallback for session ' . $sessionId);

                    $result = $this->geminiService->generateResponse(
                        $request->message,
                        $sessionId,
                        $userId
                    );

                    $cleanText = $result['response'] ?? '';
                    $products = $result['suggested_products'] ?? [];

                    // Send content as single chunk
                    if ($cleanText !== '') {
                        echo "data: " . json_encode([
                            'type' => 'content',
                            'text' => $cleanText,
                            'session_id' => $sessionId
                        ]) . "\n\n";
                        ob_flush();
                        flush();
                    }

                    // send products if any
                    if (!empty($products)) {
                        echo "data: " . json_encode([
                            'type' => 'products',
                            'products' => $products,
                            'session_id' => $sessionId
                        ]) . "\n\n";
                        ob_flush();
                        flush();
                    }

                    echo "data: " . json_encode([
                        'type' => 'done',
                        'session_id' => $sessionId
                    ]) . "\n\n";

                    ob_flush();
                    flush();

                    return;
                }

                // Normal streaming path
                $fullResponse = '';

                while (!feof($stream)) {
                    $line = fgets($stream);
                    if ($line === false) {
                        // short sleep to avoid busy loop if stream temporarily empty
                        usleep(10000);
                        continue;
                    }

                    if (strpos($line, 'data: ') === 0) {
                        $jsonStr = substr($line, 6);
                        $data = json_decode($jsonStr, true);

                        if ($data && isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                            $text = $data['candidates'][0]['content']['parts'][0]['text'];
                            $fullResponse .= $text;

                            echo "data: " . json_encode([
                                'type' => 'content',
                                'text' => $text,
                                'session_id' => $sessionId
                            ]) . "\n\n";
                            ob_flush();
                            flush();
                        }
                    }
                }

                fclose($stream);

                // Parse final response for products (ids)
                [$cleanText, $productIds] = $this->parseResponseForProducts($fullResponse, $request->message);

                // Get full product details snapshot
                $products = $this->getProductDetails($productIds);

                // Update the chat history with complete response and product snapshots
                $lastChat = ChatHistory::where('session_id', $sessionId)
                    ->where('user_message', $request->message)
                    ->latest()
                    ->first();

                if ($lastChat) {
                    $lastChat->update([
                        'bot_response' => $cleanText,
                        'suggested_products' => $products
                    ]);
                }

                // Send final message with products (full objects)
                if (!empty($products)) {
                    echo "data: " . json_encode([
                        'type' => 'products',
                        'products' => $products,
                        'session_id' => $sessionId
                    ]) . "\n\n";
                }

                echo "data: " . json_encode([
                    'type' => 'done',
                    'session_id' => $sessionId
                ]) . "\n\n";

                ob_flush();
                flush();
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no', // Disable nginx buffering
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('Stream chat error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi streaming chat'
            ], 500);
        }
    }

    private function parseResponseForProducts(string $response, string $message): array
    {
        // Same logic as in GeminiChatService
        $productIds = [];
        $cleanText = $response;

        if (preg_match('/\{.*"suggested_product_ids"\s*:\s*\[.*?\].*?\}/s', $response, $jsonMatch)) {
            $jsonStr = $jsonMatch[0];
            $decoded = json_decode($jsonStr, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['suggested_product_ids']) && is_array($decoded['suggested_product_ids'])) {
                $productIds = array_map('intval', $decoded['suggested_product_ids']);
            }
            $cleanText = trim(str_replace($jsonStr, '', $response));
        }

        return [$cleanText, array_slice($productIds, 0, 4)];
    }

    private function getProductDetails(array $productIds): array
    {
        // Same logic as in GeminiChatService
        if (empty($productIds)) {
            return [];
        }

        try {
            return \App\Models\Product::whereIn('id', $productIds)
                ->where('status', 'active')
                ->with('cat_info', 'brand')
                ->get()
                ->map(function ($product) {
                    $photos = explode(',', $product->photo ?? '');
                    return [
                        'id' => $product->id,
                        'title' => $product->title,
                        'slug' => $product->slug,
                        'price' => $product->price,
                        'discount' => $product->discount ?? 0,
                        'photo' => $photos[0] ?? '/storage/photos/default.jpg',
                        'category' => $product->cat_info->title ?? 'Không phân loại',
                        'brand' => $product->brand->title ?? 'Chưa xác định',
                        'summary' => strip_tags($product->summary ?? ''),
                        'url' => route('product-detail', $product->slug)
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting product details: ' . $e->getMessage());
            return [];
        }
    }

    public function getHistory(Request $request)
    {
        try {
            $request->validate([
                'session_id' => 'required|string'
            ]);

            $history = ChatHistory::getSessionHistory($request->session_id);

            return response()->json([
                'success' => true,
                'history' => $history
            ]);
        } catch (\Exception $e) {
            Log::error('Get history error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Không thể tải lịch sử chat'
            ], 500);
        }
    }

    public function clearHistory(Request $request)
    {
        try {
            $request->validate([
                'session_id' => 'required|string'
            ]);

            $userId = Auth::check() ? Auth::user()->id : null;

            $deleted = ChatHistory::clearSessionHistory(
                $request->session_id,
                $userId
            );

            return response()->json([
                'success' => true,
                'message' => "Đã xóa {$deleted} tin nhắn"
            ]);
        } catch (\Exception $e) {
            Log::error('Clear history error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa lịch sử chat'
            ], 500);
        }
    }

    public function newSession()
    {
        try {
            $sessionId = Str::uuid()->toString();

            return response()->json([
                'success' => true,
                'session_id' => $sessionId
            ]);
        } catch (\Exception $e) {
            Log::error('New session error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo phiên chat mới'
            ], 500);
        }
    }
}
