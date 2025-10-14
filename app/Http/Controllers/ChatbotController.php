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
                'force_search' => 'nullable'
            ]);

            $sessionId = $request->session_id ?: Str::uuid()->toString();
            $userId = Auth::check() ? Auth::user()->id : null;

            // Add user message first (for immediate display)
            $chatRecord = ChatHistory::create([
                'session_id' => $sessionId,
                'user_id' => $userId,
                'user_message' => $request->message,
                'bot_response' => '',
                'suggested_products' => []
            ]);

            $forceSearch = filter_var($request->force_search, FILTER_VALIDATE_BOOLEAN);

            // If force_search, use direct AI response instead of just keyword search
            if ($forceSearch) {
                $result = $this->geminiService->generateResponse(
                    $request->message,
                    $sessionId,
                    $userId
                );

                if ($result['success']) {
                    $cleanText = $result['response'] ?? 'Mình tìm được một vài sản phẩm phù hợp với yêu cầu của bạn.';
                    $products = $result['suggested_products'] ?? [];
                    $fallback = $result['fallback'] ?? null;

                    // Update chat record
                    $chatRecord->update([
                        'bot_response' => $cleanText,
                        'suggested_products' => $products
                    ]);

                    $response = response()->stream(function () use ($cleanText, $products, $fallback, $sessionId) {
                        // Send content
                        echo "data: " . json_encode([
                            'type' => 'content',
                            'text' => $cleanText,
                            'session_id' => $sessionId
                        ]) . "\n\n";
                        ob_flush();
                        flush();

                        // Send products if any
                        if (!empty($products)) {
                            echo "data: " . json_encode([
                                'type' => 'products',
                                'products' => $products,
                                'session_id' => $sessionId
                            ]) . "\n\n";
                            ob_flush();
                            flush();
                        }

                        // Send fallback if needed
                        if ($fallback) {
                            echo "data: " . json_encode([
                                'type' => 'fallback',
                                'fallback' => $fallback,
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
                } else {
                    // Fallback to keyword search if AI fails
                    $products = $this->geminiService->searchProductsByKeywords($request->message, 4);
                    $cleanText = !empty($products)
                        ? 'Mình tìm được một vài sản phẩm phù hợp với yêu cầu của bạn.'
                        : 'Xin lỗi, mình không tìm thấy sản phẩm phù hợp. Bạn có thể thử tìm kiếm khác không?';

                    $chatRecord->update([
                        'bot_response' => $cleanText,
                        'suggested_products' => $products
                    ]);

                    $response = response()->stream(function () use ($cleanText, $products, $sessionId) {
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
            }

            // Normal streaming path - Always use AI response, no pure streaming
            $result = $this->geminiService->generateResponse(
                $request->message,
                $sessionId,
                $userId
            );

            if ($result['success']) {
                $cleanText = $result['response'] ?? '';
                $products = $result['suggested_products'] ?? [];
                $fallback = $result['fallback'] ?? null;

                // Update chat record
                $chatRecord->update([
                    'bot_response' => $cleanText,
                    'suggested_products' => $products
                ]);

                $response = response()->stream(function () use ($cleanText, $products, $fallback, $sessionId) {
                    // Simulate typing effect by sending chunks
                    $words = explode(' ', $cleanText);
                    $chunkSize = 3; // Send 3 words at a time

                    for ($i = 0; $i < count($words); $i += $chunkSize) {
                        $chunk = implode(' ', array_slice($words, $i, $chunkSize));
                        if ($i + $chunkSize < count($words)) {
                            $chunk .= ' '; // Add space if not last chunk
                        }

                        echo "data: " . json_encode([
                            'type' => 'content',
                            'text' => $chunk,
                            'session_id' => $sessionId
                        ]) . "\n\n";
                        ob_flush();
                        flush();

                        usleep(150000); // 150ms delay between chunks for typing effect
                    }

                    // Send products if any
                    if (!empty($products)) {
                        echo "data: " . json_encode([
                            'type' => 'products',
                            'products' => $products,
                            'session_id' => $sessionId
                        ]) . "\n\n";
                        ob_flush();
                        flush();
                    }

                    // Send fallback if needed
                    if ($fallback) {
                        echo "data: " . json_encode([
                            'type' => 'fallback',
                            'fallback' => $fallback,
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
            } else {
                // Error handling
                $errorMessage = $result['message'] ?? 'Xin lỗi, đã có lỗi xảy ra. Vui lòng thử lại.';

                $chatRecord->update([
                    'bot_response' => $errorMessage,
                    'suggested_products' => []
                ]);

                $response = response()->stream(function () use ($errorMessage, $sessionId) {
                    echo "data: " . json_encode([
                        'type' => 'content',
                        'text' => $errorMessage,
                        'session_id' => $sessionId
                    ]) . "\n\n";

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
        $productIds = [];
        $cleanText = $response;
        $fallbackInfo = null;

        // Parse JSON response format
        if (preg_match('/\{[\s\S]*"response_text"[\s\S]*\}/s', $response, $jsonMatch)) {
            $jsonStr = $jsonMatch[0];
            $decoded = json_decode($jsonStr, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $cleanText = $decoded['response_text'] ?? $response;

                if (isset($decoded['suggested_products']) && is_array($decoded['suggested_products'])) {
                    foreach ($decoded['suggested_products'] as $item) {
                        if (isset($item['id'])) {
                            $productIds[] = intval($item['id']);
                        }
                    }
                }

                // Handle fallback information
                if (!empty($decoded['fallback_category']) || !empty($decoded['fallback_message'])) {
                    $fallbackInfo = [
                        'category_slug' => $decoded['fallback_category'] ?? null,
                        'message' => $decoded['fallback_message'] ?? null
                    ];
                }
            }
        }

        // Fallback to old parsing method
        if (empty($productIds)) {
            if (preg_match('/\{.*"suggested_product_ids"\s*:\s*\[.*?\].*?\}/s', $response, $jsonMatch)) {
                $jsonStr = $jsonMatch[0];
                $decoded = json_decode($jsonStr, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded['suggested_product_ids'])) {
                    $productIds = array_map('intval', $decoded['suggested_product_ids']);
                }
                $cleanText = trim(str_replace($jsonStr, '', $response));
            }
        }

        return [$cleanText, array_slice($productIds, 0, 4), $fallbackInfo];
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
