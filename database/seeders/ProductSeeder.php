<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()->createMany([
            [
                'title' => 'Đồng hồ số',
                'photo' => '/storage/photos/1/Products/dong_ho_so.jpg',
                'cat_id' => 1,
                'child_cat_id' => 7,
                'brand_id' => Brand::inRandomOrder()->take(1)->get()[0]->id,
            ],
            [
                'title' => 'Đồng hồ la mã',
                'photo' => '/storage/photos/1/Products/dong_ho_la_ma.jpg',
                'cat_id' => 1,
                'child_cat_id' => 8,
                'brand_id' => Brand::inRandomOrder()->take(1)->get()[0]->id,
            ],
        ]);
    }
}
