<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parents = Category::factory()->createMany([
            [
                'title' => 'Đồng hồ gia dụng',
                'slug' => 'household',
                'summary' => 'Đồng hồ gia dụng',
                'photo' => '/storage/photos/1/Category/dong_ho_treo_tuong.jpg',
                'is_parent' => true,
                'status' => 'active',
            ],
            [
                'title' => 'Đồng hồ báo thức',
                'slug' => 'alarm',
                'summary' => 'Đồng hồ báo thức',
                'photo' => '/storage/photos/1/Category/dong_ho_bao_thuc.jpg',
                'is_parent' => true,
                'status' => 'active',
            ],
            [
                'title' => 'Đồng hồ con lắc',
                'slug' => 'pendulum',
                'summary' => 'Đồng hồ con lắc',
                'photo' => '/storage/photos/1/Category/dong_ho_con_lac.jpg',
                'is_parent' => true,
                'status' => 'active',
            ],
            [
                'title' => 'Đồng hồ đeo tay',
                'slug' => 'watches',
                'summary' => 'Đồng hồ đeo tay',
                'photo' => '/storage/photos/1/Category/dong_ho_rolex.jpg',
                'is_parent' => true,
                'status' => 'active',
            ],
        ]);

        // Children for household clocks.
        $householdClocks = Category::factory()->createMany([
            [
                'title' => 'Đồng hồ treo tường',
                'slug' => 'wall',
                'summary' => 'Đồng hồ treo tường',
                'photo' => '/storage/photos/1/Category/dong_ho_treo_tuong.jpg',
                'is_parent' => false,
                'status' => 'active',
                'parent_id' => $parents[0]->id,
            ],
            [
                'title' => 'Đồng hồ để bàn',
                'slug' => 'table',
                'summary' => 'Đồng hồ để bàn',
                'photo' => '/storage/photos/1/Category/dong_ho_de_ban.jpg',
                'is_parent' => false,
                'status' => 'active',
                'parent_id' => $parents[0]->id,
            ]
        ]);

        // Children for wall clocks.
        Category::factory()->createMany([
            [
                'title' => 'Đồng hồ số',
                'slug' => 'number',
                'summary' => 'Đồng hồ số',
                'photo' => '/storage/photos/1/Category/dong_ho_so.jpg',
                'is_parent' => false,
                'status' => 'active',
                'parent_id' => $householdClocks[0]->id,
            ],
            [
                'title' => 'Đồng hồ la mã',
                'slug' => 'roman',
                'summary' => 'Đồng hồ la mã',
                'photo' => '/storage/photos/1/Category/dong_ho_la_ma.jpg',
                'is_parent' => false,
                'status' => 'active',
                'parent_id' => $householdClocks[0]->id,
            ],
        ]);

        // Children for watches.
        Category::factory()->createMany([
            [
                'title' => 'Đồng hồ cơ',
                'slug' => 'analog',
                'summary' => 'Đồng hồ cơ',
                'photo' => '/storage/photos/1/Category/dong_ho_co.jpg',
                'is_parent' => false,
                'status' => 'active',
                'parent_id' => $parents[3]->id,
            ],
            [
                'title' => 'Đồng hồ điện tử',
                'slug' => 'digital',
                'summary' => 'Đồng hồ điện tử',
                'photo' => '/storage/photos/1/Category/dong_ho_dien-tu.jpg',
                'is_parent' => false,
                'status' => 'active',
                'parent_id' => $parents[3]->id,
            ],
        ]);
    }
}
