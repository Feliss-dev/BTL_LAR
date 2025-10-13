<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productImages = Storage::disk('public')->files('photos/1/Products');

        $categorySets = [
            1 => [5, 6, 7, 8],
            2 => [9, 10],
            3 => null,
            4 => null,
        ];

        Product::factory()->count(125)->sequence(function ($sequence) use ($productImages, $categorySets) {
            $image = $productImages[array_rand($productImages)];

            $categoryId = array_rand($categorySets);
            $childCategoryId = $categorySets[$categoryId] == null ? null : $categorySets[$categoryId][array_rand($categorySets[$categoryId])];

            return [
                'title' => $this->humanizeFilename($image),
                'photo' => '/storage/' . $image,
                'cat_id' => $categoryId,
                'child_cat_id' => $childCategoryId,
                'brand_id' => Brand::inRandomOrder()->take(1)->get()[0]->id,
            ];
        })->create();
    }

    private function humanizeFilename(string $path): string
    {
        // Lấy filename không chứa extension
        $name = pathinfo($path, PATHINFO_FILENAME);

        // Thay dấu gạch dưới/gạch ngang thành dấu cách
        $name = preg_replace('/[_-]+/', ' ', $name);

        // Xóa tiền tố số nếu có (ví dụ 1612870846-...)
        $name = preg_replace('/^[0-9]+\s*/', '', $name);

        // mapping từ token ascii -> từ có dấu/chuẩn tiếng Việt
        $map = [
            'dong' => 'đồng',
            'ho' => 'hồ',
            'bao' => 'báo',
            'thuc' => 'thức',
            'co' => 'cổ',
            'con' => 'con',
            'lac' => 'lắc',
            'de' => 'để',
            'ban' => 'bàn',
            'dien' => 'điện',
            'tu' => 'tử',
            'la' => 'La',
            'ma' => 'mã',
            'rolex' => 'Rolex',
            'so' => 'số',
            'treo' => 'treo',
            'tuong' => 'tường',
            // Thêm mapping khác nếu cần
        ];

        $words = preg_split('/\s+/', mb_strtolower(trim($name), 'UTF-8'));

        $mapped = array_map(function ($w) use ($map) {
            return $map[$w] ?? $w;
        }, $words);

        // Viết hoa mỗi từ theo chuẩn Unicode
        $title = mb_convert_case(implode(' ', $mapped), MB_CASE_TITLE, 'UTF-8');

        return $title;
    }
}
