<?php

namespace App\Services;

use App\Models\ChatHistory;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiChatService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-2.5-flash');
    }

    public function generateResponse(string $message, string $sessionId, ?int $userId = null): array
    {
        try {
            if (empty($this->apiKey)) {
                throw new \Exception('Gemini API key is not configured');
            }

            $productContext = $this->getProductContext();
            $chatHistory = ChatHistory::getSessionHistory($sessionId, 10);

            // Build conversation context với format role-based
            $conversationContents = $this->buildConversationContents($chatHistory, $message, $productContext);

            $rawResponse = $this->callGeminiAPIWithContext($conversationContents);

            // Parse response into clean text + product ids
            [$cleanText, $productIds] = $this->parseResponseAndProducts($rawResponse, $message);

            // get full product details snapshot (image, url, price, brand, summary)
            $productDetails = $this->getProductDetails($productIds);

            // Save chat (store snapshot of suggested products)
            ChatHistory::create([
                'session_id' => $sessionId,
                'user_id' => $userId,
                'user_message' => $message,
                'bot_response' => $cleanText,
                'suggested_products' => $productDetails
            ]);

            return [
                'success' => true,
                'response' => $cleanText,
                'suggested_products' => $productDetails,
                'session_id' => $sessionId
            ];
        } catch (\Exception $e) {
            Log::error('Gemini Chat Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'session_id' => $sessionId,
                'message' => $message
            ]);

            return [
                'success' => false,
                'message' => 'Xin lỗi, tôi đang gặp sự cố. Vui lòng thử lại sau.',
                'error' => $e->getMessage(),
                'session_id' => $sessionId
            ];
        }
    }

    public function streamResponse(string $message, string $sessionId, ?int $userId = null)
    {
        try {
            if (empty($this->apiKey)) {
                throw new \Exception('Gemini API key is not configured');
            }

            $productContext = $this->getProductContext();
            $chatHistory = ChatHistory::getSessionHistory($sessionId, 10);

            $conversationContents = $this->buildConversationContents($chatHistory, $message, $productContext);

            return $this->callGeminiStreamAPI($conversationContents);
        } catch (\Exception $e) {
            Log::error('Gemini Stream Error: ' . $e->getMessage());
            throw $e;
        }
    }

    // trước đây: private function buildConversationContents(array $chatHistory, string $newMessage, string $productContext): array
    private function buildConversationContents(iterable $chatHistory, string $newMessage, string $productContext): array
    {
        $contents = [];

        // System instruction as first user message
        $systemPrompt = "
Bạn là một tư vấn viên bán hàng chuyên nghiệp cho cửa hàng đồng hồ. Mục tiêu: luôn đưa ra tư vấn ngắn (1-3 câu) và kèm theo danh sách gợi ý sản phẩm phù hợp (tối đa 4 sản phẩm).

QUY TẮC BẮT BUỘC:
- Trả lời bằng tiếng Việt, thân thiện, chuyên nghiệp, ngắn gọn (<= 120 từ).
- NGAY SAU ĐOẠN TƯ VẤN hãy ĐÍNH KÈM 1 KHỐI JSON duy nhất (trên 1 block, KHÔNG có text xen giữa) với dạng:
  {\"suggested_product_ids\": [ID_OR_SKU_OR_SLUG_1, ID_OR_SKU_OR_SLUG_2, ...]}
  - IDs có thể là số (id DB) hoặc chuỗi (sku/slug). Không gửi quá 4 items.
- Nếu không tìm thấy sản phẩm phù hợp, trả lời 1 câu hỏi ngắn để làm rõ. Không suy đoán sản phẩm khi thiếu dữ kiện.

Ví dụ (bắt buộc theo cấu trúc):
Ví dụ trả lời:
\"Bạn hợp với phong cách cổ điển, gợi ý 2 mẫu mạ vàng, dây da.\"
{\"suggested_product_ids\": [123, \"classic-gold-01\"]}

Dưới đây là dữ liệu cửa hàng (dùng chính xác id/slug/sku khi tham chiếu):
{$productContext}
        ";

        // Start conversation context
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $systemPrompt]]
        ];

        // Friendly confirmation
        $contents[] = [
            'role' => 'model',
            'parts' => [['text' => 'Tôi hiểu. Tôi sẽ tư vấn ngắn gọn và kèm gợi ý sản phẩm khi phù hợp.']]
        ];

        // Add previous conversations (if any)
        if (!empty($chatHistory)) {
            foreach ($chatHistory as $chat) {
                $contents[] = [
                    'role' => 'user',
                    'parts' => [['text' => $chat->user_message]]
                ];
                $contents[] = [
                    'role' => 'model',
                    'parts' => [['text' => $chat->bot_response]]
                ];
            }
        }

        // Add current user message
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $newMessage]]
        ];

        return $contents;
    }

    private function callGeminiAPIWithContext(array $contents): string
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";

            $payload = [
                'contents' => $contents,
                'generationConfig' => [
                    // lower temperature for deterministic outputs (less hỏi/đoán)
                    'temperature' => 0.0,
                    'maxOutputTokens' => 2000,
                    'topK' => 40,
                    'topP' => 0.9,
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'x-goog-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($url, $payload);

            Log::info('Gemini API Response Status: ' . $response->status());

            if (!$response->successful()) {
                $errorBody = $response->body();
                Log::error('Gemini API Error: ' . $errorBody);
                throw new \Exception('API call failed with status ' . $response->status() . ': ' . $errorBody);
            }

            $data = $response->json();
            Log::info('Gemini API Response Data:', $data);

            if (isset($data['candidates']) && is_array($data['candidates']) && count($data['candidates']) > 0) {
                $candidate = $data['candidates'][0];
                $text = $this->parseCandidateText($candidate);
                if ($text !== null) {
                    return $text;
                }
                throw new \Exception('Unable to extract text from candidate');
            }

            throw new \Exception('No candidates found in API response');
        } catch (\Exception $e) {
            Log::error('Gemini API Call Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    private function callGeminiStreamAPI(array $contents)
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:streamGenerateContent";

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.8,
                'maxOutputTokens' => 2000,
                'topK' => 40,
                'topP' => 0.95,
            ]
        ];

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => [
                    'x-goog-api-key: ' . $this->apiKey,
                    'Content-Type: application/json'
                ],
                'content' => json_encode($payload),
                'timeout' => 60
            ]
        ]);

        // suppress warnings with @fopen (so PHP won't convert warning to ErrorException)
        $stream = @fopen($url . '?alt=sse', 'r', false, $context);

        // If cannot open stream (e.g. 503), return false to let controller fallback gracefully
        if ($stream === false) {
            Log::warning('Gemini SSE stream open failed, falling back to sync generateContent', [
                'url' => $url,
            ]);
            return false;
        }

        return $stream;
    }

    private function parseResponseAndProducts(string $rawResponse, string $message): array
    {
        $productIds = [];
        $cleanText = $rawResponse;

        // 1) Try extract JSON block {"suggested_product_ids": [...]}
        if (preg_match('/\{[^}]*"suggested_product_ids"\s*:\s*\[.*?\][^}]*\}/s', $rawResponse, $jsonMatch)) {
            $jsonStr = $jsonMatch[0];
            $decoded = json_decode($jsonStr, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['suggested_product_ids']) && is_array($decoded['suggested_product_ids'])) {
                foreach ($decoded['suggested_product_ids'] as $ident) {
                    $mapped = $this->mapIdentifierToProductId($ident);
                    if ($mapped !== null) {
                        $productIds[] = $mapped;
                    }
                }
            }
            // remove JSON block from text for clean bot text
            $cleanText = trim(str_replace($jsonStr, '', $rawResponse));
        }

        // 2) If none found, try to parse numeric IDs from text
        if (empty($productIds)) {
            preg_match_all('/\bID[:#]?\s*(\d+)\b/i', $rawResponse, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $mid) {
                    $productIds[] = intval($mid);
                }
            }
        }

        // 3) As last resort, try to infer from message keywords
        if (empty($productIds)) {
            $productIds = $this->getSuggestedProductIdsByKeywords($message);
        }

        // Deduplicate and limit to 4
        $productIds = array_values(array_slice(array_values(array_unique($productIds)), 0, 4));

        return [$cleanText, $productIds ?: []];
    }

    /**
     * Accept identifier which can be numeric id, sku, slug or free string.
     * Returns product id (int) or null.
     */
    private function mapIdentifierToProductId($ident): ?int
    {
        if ($ident === null) {
            return null;
        }

        // if numeric string or int -> return as int (verify exists)
        if (is_numeric($ident)) {
            $id = intval($ident);
            $exists = Product::where('id', $id)->where('status', 'active')->exists();
            return $exists ? $id : null;
        }

        // string identifiers: try exact sku, exact slug, exact code, then title LIKE
        $identStr = trim((string)$ident);
        if ($identStr === '') {
            return null;
        }

        $prod = Product::where('status', 'active')
            ->where(function ($q) use ($identStr) {
                $q->where('sku', $identStr)
                    ->orWhere('slug', $identStr)
                    ->orWhere('code', $identStr);
            })
            ->first();

        if ($prod) {
            return intval($prod->id);
        }

        // fallback: try fuzzy title/summary/brand/cat match
        $prod = Product::where('status', 'active')
            ->where(function ($q) use ($identStr) {
                $q->where('title', 'LIKE', "%{$identStr}%")
                    ->orWhere('summary', 'LIKE', "%{$identStr}%");
            })
            ->with(['brand', 'cat_info'])
            ->first();

        if ($prod) {
            return intval($prod->id);
        }

        return null;
    }

    private function getSuggestedProductIdsByKeywords(string $message): array
    {
        try {
            $messageLower = mb_strtolower($message, 'UTF-8');
            $tokens = preg_split('/[^\p{L}\p{N}]+/u', $messageLower);
            $tokens = array_filter(array_map('trim', $tokens), fn($t) => mb_strlen($t, 'UTF-8') > 2);

            if (empty($tokens)) {
                return [];
            }

            $prodQuery = Product::where('status', 'active')
                ->where(function ($q) use ($tokens) {
                    foreach ($tokens as $t) {
                        $q->orWhere('title', 'LIKE', "%{$t}%")
                            ->orWhere('summary', 'LIKE', "%{$t}%")
                            ->orWhereHas('brand', function ($b) use ($t) {
                                $b->where('title', 'LIKE', "%{$t}%");
                            })
                            ->orWhereHas('cat_info', function ($c) use ($t) {
                                $c->where('title', 'LIKE', "%{$t}%");
                            });
                    }
                })
                ->limit(8);

            $ids = $prodQuery->pluck('id')->toArray();

            return array_slice(array_map('intval', array_values(array_unique($ids))), 0, 4);
        } catch (\Exception $e) {
            Log::error('Keyword suggestion error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Robust extractor that traverses common response shapes and returns the first text found.
     */
    private function parseCandidateText($candidate): ?string
    {
        // candidate could be string
        if (is_string($candidate)) {
            return $candidate;
        }

        // If candidate has 'content' and it's a string
        if (isset($candidate['content']) && is_string($candidate['content'])) {
            return $candidate['content'];
        }

        // If content is an array with 'parts'
        if (isset($candidate['content']) && is_array($candidate['content'])) {
            $content = $candidate['content'];

            // shape: content => ['parts' => [ ['text' => '...'], ... ] ]
            if (isset($content['parts']) && is_array($content['parts'])) {
                $texts = [];
                foreach ($content['parts'] as $part) {
                    if (is_array($part) && isset($part['text'])) {
                        $texts[] = $part['text'];
                    } elseif (is_string($part)) {
                        $texts[] = $part;
                    }
                }
                if (!empty($texts)) {
                    return implode("\n", $texts);
                }
            }

            // shape: content is list of parts directly
            $texts = [];
            foreach ($content as $item) {
                if (is_array($item) && isset($item['text'])) {
                    $texts[] = $item['text'];
                } elseif (is_string($item)) {
                    $texts[] = $item;
                }
            }
            if (!empty($texts)) {
                return implode("\n", $texts);
            }
        }

        // Candidate may have other nested fields. Try to find any 'text' recursively.
        $found = $this->findFirstTextRecursive($candidate);
        return $found;
    }

    private function findFirstTextRecursive($node)
    {
        if (is_string($node)) {
            return $node;
        }
        if (is_array($node)) {
            if (isset($node['text']) && is_string($node['text'])) {
                return $node['text'];
            }
            foreach ($node as $child) {
                $res = $this->findFirstTextRecursive($child);
                if ($res !== null) {
                    return $res;
                }
            }
        }
        return null;
    }

    /**
     * Return product context used inside model prompt.
     * Now includes compact inventory rows with more fields so LLM can reference exact products.
     */
    private function getProductContext(): string
    {
        try {
            $categories = Category::with('child_cat')
                ->where('status', 'active')
                ->where('is_parent', true)
                ->get();

            // include a larger set so model can reference exact ids/slugs
            $featuredProducts = Product::where('status', 'active')
                ->orderByDesc('is_featured')
                ->orderByDesc('updated_at')
                ->with('cat_info', 'brand')
                ->limit(80)
                ->get();

            $context = "THÔNG TIN CỬA HÀNG:\n";
            $context .= "Danh mục (id | slug | title):\n";
            foreach ($categories as $category) {
                $context .= "{$category->id} | {$category->slug} | {$category->title}\n";
                if ($category->child_cat && $category->child_cat->count() > 0) {
                    foreach ($category->child_cat as $child) {
                        $context .= "  {$child->id} | {$child->slug} | {$child->title}\n";
                    }
                }
            }

            $context .= "\nGHI CHÚ CHO BỘ AI:\n";
            $context .= "- Khi gợi ý sản phẩm, NHẤT ĐỊNH đính kèm 1 khối JSON duy nhất ở cuối response:\n";
            $context .= "  {\"suggested_product_ids\": [ID_OR_SLUG_OR_SKU, ...]}\n";
            $context .= "- Không quá 4 items. Sử dụng chính xác id hoặc slug nếu có.\n";
            $context .= "- Nếu không tìm đủ dữ liệu, hỏi 1 câu ngắn để làm rõ.\n\n";

            $context .= "MỘT SỐ SẢN PHẨM (id | slug | title | price | discount | stock | category_id | category_title | brand | url | photo):\n";
            foreach ($featuredProducts as $product) {
                $photo = explode(',', $product->photo ?? '')[0] ?? '/storage/photos/default.jpg';
                $url = route('product-detail', $product->slug);
                $catId = $product->cat_info->id ?? 0;
                $catTitle = $product->cat_info->title ?? 'Không phân loại';
                $brand = $product->brand->title ?? '';
                $price = intval($product->price);
                $discount = floatval($product->discount ?? 0);
                $stock = intval($product->qty ?? 0);

                // single-line record for reliable parsing
                $context .= "{$product->id} | {$product->slug} | {$product->title} | {$price} | {$discount} | {$stock} | {$catId} | {$catTitle} | {$brand} | {$url} | {$photo}\n";
            }

            return $context;
        } catch (\Exception $e) {
            Log::error('Error getting product context: ' . $e->getMessage());
            return "THÔNG TIN CỬA HÀNG: chúng tôi bán đồng hồ các loại.";
        }
    }

    /**
     * Public helper: search products by keywords and return product detail snapshots.
     * Used by controller when force_search flag is set (quick prompts).
     */
    public function searchProductsByKeywords(string $message, int $limit = 4): array
    {
        $ids = $this->getSuggestedProductIdsByKeywords($message);
        if (empty($ids)) {
            return [];
        }
        return $this->getProductDetails(array_slice($ids, 0, $limit));
    }

    private function formatChatHistory($chatHistory): string
    {
        $formatted = "";
        foreach ($chatHistory as $chat) {
            $formatted .= "Khách hàng: {$chat->user_message}\n";
            $formatted .= "Tư vấn viên: {$chat->bot_response}\n\n";
        }
        return $formatted;
    }

    private function getProductDetails(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        try {
            $products = Product::whereIn('id', $productIds)
                ->where('status', 'active')
                ->with('cat_info', 'brand')
                ->get()
                ->keyBy('id');

            $result = [];
            foreach ($productIds as $id) {
                if (isset($products[$id])) {
                    $p = $products[$id];
                    $photos = explode(',', $p->photo ?? '');
                    $result[] = [
                        'id' => $p->id,
                        'title' => $p->title,
                        'slug' => $p->slug,
                        'price' => $p->price,
                        'discount' => $p->discount ?? 0,
                        'photo' => $photos[0] ?? '/storage/photos/default.jpg',
                        'category' => $p->cat_info->title ?? 'Không phân loại',
                        'brand' => $p->brand->title ?? 'Chưa xác định',
                        'summary' => strip_tags($p->summary ?? ''),
                        'url' => route('product-detail', $p->slug)
                    ];
                }
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error getting product details: ' . $e->getMessage());
            return [];
        }
    }
}
