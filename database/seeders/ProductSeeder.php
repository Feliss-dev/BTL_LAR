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

        Product::factory()->count(125)->sequence(function ($sequence) use ($productImages) {
            $image = $productImages[array_rand($productImages)];

            return [
                'title' => pathinfo($image, PATHINFO_FILENAME),
                'photo' => '/storage/' . $image,
                'cat_id' => 1,
                'child_cat_id' => rand(7, 8),
                'brand_id' => Brand::inRandomOrder()->take(1)->get()[0]->id,
            ];
        })->create();
    }
}
