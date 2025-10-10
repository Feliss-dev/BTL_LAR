<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::get();

        foreach (Product::all() as $product) {
            $reviewCount = rand(0, 6);
            $reviewUsers = $users->random($reviewCount);

            ProductReview::factory($reviewCount)->sequence(function (Sequence $sequence) use ($product, $reviewUsers) {
                return [
                    'product_id' => $product->id,
                    'user_id' => $reviewUsers[$sequence->index]->id,
                ];
            })->create();
        }
    }
}
