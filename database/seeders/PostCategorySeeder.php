<?php

namespace Database\Seeders;

use App\Models\PostCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PostCategory::factory()->createMany([
            [
                'title' => 'Hệ thống',
                'slug' => 'system',
            ],
            [
                'title' => 'Bảo mật',
                'slug' => 'security',
            ],
            [
                'title' => 'Sản phẩm',
                'slug' => 'product',
            ],
            [
                'title' => 'Giảm giá',
                'slug' => 'sale'
            ],
            [
                'title' => 'Sự kiện',
                'slug' => 'event'
            ],
        ]);
    }
}
