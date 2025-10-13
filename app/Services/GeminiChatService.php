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
        $this->model = config('services.gemini.model', 'gemini-2.0-flash-exp');
    }

    // Add missing streamResponse method
    public function streamResponse(string $message, string $sessionId, ?int $userId = null)
    {
        try {
            $productContext = $this->getProductContext();
            $chatHistory = ChatHistory::getSessionHistory($sessionId, 10);
            $conversationContents = $this->buildConversationContents($chatHistory, $message, $productContext);

            return $this->callGeminiStreamAPI($conversationContents);
        } catch (\Exception $e) {
            Log::error('Stream response error: ' . $e->getMessage());
            return false;
        }
    }

    // Add missing streamChat method
    public function streamChat(string $message, string $sessionId, ?int $userId = null)
    {
        return $this->streamResponse($message, $sessionId, $userId);
    }

    private function buildConversationContents(iterable $chatHistory, string $newMessage, string $productContext): array
    {
        $contents = [];

        // System instruction with keyword mapping
        $systemPrompt = "
Bạn là tư vấn viên bán hàng đồng hồ chuyên nghiệp. LUÔN trả lời theo format JSON chính xác.

QUY TẮC BẮT BUỘC:
1. Trả lời ngắn gọn (1-3 câu), thân thiện, chuyên nghiệp
2. LUÔN kết thúc bằng JSON duy nhất với cấu trúc:
{
  \"response_text\": \"Câu trả lời tư vấn\",
  \"suggested_products\": [
    {\"id\": product_id, \"reason\": \"lý do gợi ý\"}
  ],
  \"fallback_category\": \"category_slug hoặc null\",
  \"fallback_message\": \"thông báo hết hàng hoặc null\"
}

3. MAPPING TỪ KHÓA QUAN TRỌNG:
- 'báo thức' = dong_ho_bao_thuc
- 'treo tường' = dong_ho_treo_tuong
- 'cổ điển' = dong_ho_co_dien
- 'cổ' = dong_ho_co
- 'sợi dây' = dong_ho_day
- 'cơ học' = dong_ho_co_hoc
- 'số' = dong_ho_so
- 'kim' = dong_ho_kim

4. LOGIC GỢI Ý:
- Tìm sản phẩm theo từ khóa và tên slug
- Ưu tiên sản phẩm có stock > 0
- Nếu có >= 2 sản phẩm: chỉ điền suggested_products
- Nếu < 2 sản phẩm: thêm fallback_category + fallback_message

DANH SÁCH SẢN PHẨM:
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
            'parts' => [['text' => 'Tôi hiểu. Tôi sẽ trả lời theo format JSON chuẩn với gợi ý sản phẩm phù hợp.

{"response_text": "Tôi hiểu yêu cầu. Tôi sẽ tư vấn và gợi ý sản phẩm phù hợp.", "suggested_products": [], "fallback_category": null, "fallback_message": null}']]
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

        $stream = @fopen($url . '?alt=sse', 'r', false, $context);

        if ($stream === false) {
            Log::warning('Gemini SSE stream open failed, falling back to sync generateContent', [
                'url' => $url,
            ]);
            return false;
        }

        return $stream;
    }

    // Add missing parseCandidateText method
    private function parseCandidateText($candidate): ?string
    {
        try {
            if (isset($candidate['content']['parts']) && is_array($candidate['content']['parts'])) {
                $parts = $candidate['content']['parts'];
                $text = '';
                foreach ($parts as $part) {
                    if (isset($part['text'])) {
                        $text .= $part['text'];
                    }
                }
                return $text;
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Error parsing candidate text: ' . $e->getMessage());
            return null;
        }
    }

    // Add missing mapIdentifierToProductId method
    private function mapIdentifierToProductId($identifier): ?int
    {
        try {
            // If it's already a number, return as int
            if (is_numeric($identifier)) {
                return intval($identifier);
            }

            // Try to find by slug
            $product = Product::where('slug', $identifier)
                ->where('status', 'active')
                ->first();

            return $product ? $product->id : null;
        } catch (\Exception $e) {
            Log::error('Error mapping identifier to product ID: ' . $e->getMessage());
            return null;
        }
    }

    // Add missing getSuggestedProductIdsByKeywords method
    private function getSuggestedProductIdsByKeywords(string $message): array
{
    try {
        $messageLower = mb_strtolower($message, 'UTF-8');

        // Enhanced keyword mapping
        $keywordMappings = [
            'báo thức' => ['dong_ho_bao_thuc', 'bao_thuc', 'alarm'],
            'treo tường' => ['dong_ho_treo_tuong', 'treo_tuong', 'wall'],
            'cổ điển' => ['dong_ho_co_dien', 'co_dien', 'classic'],
            'cổ' => ['dong_ho_co', '_co_'],
            'số' => ['dong_ho_so', '_so'],
            'cơ' => ['dong_ho_co', 'co_hoc', 'mechanical'],
            'dây' => ['dong_ho_day', '_day'],
            'con lắc' => ['dong_ho_con_lac', 'con_lac', 'pendulum']
        ];

        $searchTerms = [$messageLower];

        // Add mapped terms
        foreach ($keywordMappings as $keyword => $mappings) {
            if (strpos($messageLower, $keyword) !== false) {
                $searchTerms = array_merge($searchTerms, $mappings);
            }
        }

        $products = Product::where('status', 'active')

            ->where(function ($query) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $query->orWhereRaw('LOWER(title) LIKE ?', ['%' . $term . '%'])
                          ->orWhereRaw('LOWER(slug) LIKE ?', ['%' . $term . '%'])
                          ->orWhereRaw('LOWER(summary) LIKE ?', ['%' . $term . '%'])
                          ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $term . '%']);
                }
            })
            ->orderByDesc('is_featured')

            ->limit(4)
            ->pluck('id')
            ->toArray();

        return $products;
    } catch (\Exception $e) {
        Log::error('Error getting suggested products by keywords: ' . $e->getMessage());
        return [];
    }
}

    private function parseResponseAndProducts(string $rawResponse, string $message): array
    {
        $productIds = [];
        $cleanText = $rawResponse;
        $fallbackCategory = null;
        $fallbackMessage = null;

        // Parse JSON response
        if (preg_match('/\{[\s\S]*"response_text"[\s\S]*\}/s', $rawResponse, $jsonMatch)) {
            $jsonStr = $jsonMatch[0];
            $decoded = json_decode($jsonStr, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $cleanText = $decoded['response_text'] ?? $rawResponse;
                $fallbackCategory = $decoded['fallback_category'] ?? null;
                $fallbackMessage = $decoded['fallback_message'] ?? null;

                if (isset($decoded['suggested_products']) && is_array($decoded['suggested_products'])) {
                    foreach ($decoded['suggested_products'] as $item) {
                        if (isset($item['id'])) {
                            $mapped = $this->mapIdentifierToProductId($item['id']);
                            if ($mapped !== null) {
                                $productIds[] = $mapped;
                            }
                        }
                    }
                }
            }
        }

        // Fallback to old parsing if JSON failed
        if (empty($productIds) && empty($fallbackCategory)) {
            $productIds = $this->getSuggestedProductIdsByKeywords($message);
        }

        // Validate products have stock
        $validProductIds = $this->filterProductsWithStock($productIds);

        // If insufficient products, get fallback category
        if (count($validProductIds) < 2 && empty($fallbackCategory)) {
            $fallbackCategory = $this->suggestFallbackCategory($message);
            if ($fallbackCategory && empty($fallbackMessage)) {
                $fallbackMessage = "Hiện tại sản phẩm này đang hết hàng. Bạn có thể xem thêm các sản phẩm tương tự trong danh mục này.";
            }
        }

        return [$cleanText, $validProductIds, $fallbackCategory, $fallbackMessage];
    }

    private function filterProductsWithStock(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        try {
            $validIds = Product::whereIn('id', $productIds)
                ->where('status', 'active')
                ->pluck('id')
                ->toArray();

            return array_values(array_unique($validIds));
        } catch (\Exception $e) {
            Log::error('Error filtering products with stock: ' . $e->getMessage());
            return $productIds;
        }
    }

    private function suggestFallbackCategory(string $message): ?string
    {
        try {
            $messageLower = mb_strtolower($message, 'UTF-8');

            $categoryMappings = [
                'nam' => 'dong-ho-nam',
                'nữ' => 'dong-ho-nu',
                'thể thao' => 'dong-ho-the-thao',
                'cao cấp' => 'dong-ho-cao-cap',
                'giá rẻ' => 'dong-ho-gia-re',
                'thông minh' => 'dong-ho-thong-minh'
            ];

            foreach ($categoryMappings as $keyword => $slug) {
                if (strpos($messageLower, $keyword) !== false) {
                    $exists = Category::where('slug', $slug)->where('status', 'active')->exists();
                    if ($exists) {
                        return $slug;
                    }
                }
            }

            $mainCategory = Category::where('status', 'active')
                ->where('is_parent', true)
                ->first();

            return $mainCategory ? $mainCategory->slug : null;
        } catch (\Exception $e) {
            Log::error('Error suggesting fallback category: ' . $e->getMessage());
            return null;
        }
    }

    public function generateResponse(string $message, string $sessionId, ?int $userId = null): array
    {
        try {
            if (empty($this->apiKey)) {
                throw new \Exception('Gemini API key is not configured');
            }

            $productContext = $this->getProductContext();
            $chatHistory = ChatHistory::getSessionHistory($sessionId, 10);

            $conversationContents = $this->buildConversationContents($chatHistory, $message, $productContext);
            $rawResponse = $this->callGeminiAPIWithContext($conversationContents);

            [$cleanText, $productIds, $fallbackCategory, $fallbackMessage] = $this->parseResponseAndProducts($rawResponse, $message);

            $productDetails = $this->getProductDetails($productIds);

            $finalResponse = $cleanText;
            $responseData = [
                'success' => true,
                'response' => $finalResponse,
                'suggested_products' => $productDetails,
                'session_id' => $sessionId
            ];

            if (!empty($fallbackCategory) || !empty($fallbackMessage)) {
                $responseData['fallback'] = [
                    'category_slug' => $fallbackCategory,
                    'message' => $fallbackMessage,
                    'category_url' => $fallbackCategory ? route('product-cat', $fallbackCategory) : null
                ];

                if ($fallbackMessage) {
                    $finalResponse .= "\n\n" . $fallbackMessage;
                    if ($fallbackCategory) {
                        $finalResponse .= " [Xem danh mục](" . route('product-cat', $fallbackCategory) . ")";
                    }
                }
            }

            ChatHistory::create([
                'session_id' => $sessionId,
                'user_id' => $userId,
                'user_message' => $message,
                'bot_response' => $finalResponse,
                'suggested_products' => $productDetails
            ]);

            return $responseData;
        } catch (\Exception $e) {
            Log::error('Gemini Chat Error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Xin lỗi, tôi đang gặp sự cố. Vui lòng thử lại sau.',
                'error' => $e->getMessage(),
                'session_id' => $sessionId
            ];
        }
    }

    private function getProductContext(): string
    {
        try {
            $categories = Category::with('child_cat')
                ->where('status', 'active')
                ->where('is_parent', true)
                ->get();

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

            $context .= "\nQUY TẮC QUAN TRỌNG:\n";
            $context .= "- Ưu tiên sản phẩm có stock > 0 (còn hàng)\n";
            $context .= "- Nếu < 2 sản phẩm còn hàng: thêm fallback_category và fallback_message\n";
            $context .= "- Luôn trả về JSON với cấu trúc chuẩn\n\n";

            $context .= "SẢN PHẨM (id | slug | title | price | discount | stock | category_slug | brand):\n";
            foreach ($featuredProducts as $product) {
                $catSlug = $product->cat_info->slug ?? 'khac';
                $brand = $product->brand->title ?? '';
                $price = intval($product->price);
                $discount = floatval($product->discount ?? 0);
                $stock = intval($product->qty ?? 0);
                $stockStatus = $stock > 0 ? "CÒN_HÀNG" : "HẾT_HÀNG";

                $context .= "{$product->id} | {$product->slug} | {$product->title} | {$price} | {$discount} | {$stock} | {$catSlug} | {$brand} | {$stockStatus}\n";
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
