<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            SettingSeeder::class,
            CouponSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            ShippingSeeder::class,
            ProductSeeder::class,
            ProductReviewSeeder::class,

            PostCategorySeeder::class,
            PostTagSeeder::class,
            PostSeeder::class,
            PostCommentSeeder::class,

            CartSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
