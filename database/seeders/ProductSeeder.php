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
            1 => [ 5, 6, 7, 8 ],
            2 => [ 9, 10 ],
            3 => null,
            4 => null,
        ];

        Product::factory()->count(125)->sequence(function ($sequence) use ($productImages, $categorySets) {
            $image = $productImages[array_rand($productImages)];

            $categoryId = array_rand($categorySets);
            $childCategoryId = $categorySets[$categoryId] == null ? null : $categorySets[$categoryId][array_rand($categorySets[$categoryId])];

            return [
                'title' => pathinfo($image, PATHINFO_FILENAME),
                'photo' => '/storage/' . $image,
                'cat_id' => $categoryId,
                'child_cat_id' => $childCategoryId,
                'brand_id' => Brand::inRandomOrder()->take(1)->get()[0]->id,
            ];
        })->create();
    }
}
