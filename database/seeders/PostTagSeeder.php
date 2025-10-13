<?php

namespace Database\Seeders;

use App\Models\PostTag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PostTag::factory()->createMany([
            [
                'title' => 'Hệ thống',
                'slug' => 'system',
            ],
            [
                'title' => 'Sự kiện',
                'slug' => 'event',
            ],
            [
                'title' => 'Sản phẩm',
                'slug' => 'product',
            ],
        ]);
    }
}
