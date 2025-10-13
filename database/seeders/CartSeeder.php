<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Product;
use App\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        foreach (User::all() as $user) {
            $products = Product::inRandomOrder()->take(rand(4, 7))->get();

            Cart::factory($products->count())->sequence(function (Sequence $sequence) use ($products, $user) {
                $count = rand(1, 3);
                $product = $products[$sequence->index];

                return [
                    'user_id' => $user->id,
                    'product_id' => $product,
                    'amount' => $product->price * $count,
                    'price' => $product->price * $count,
                    'quantity' => $count,
                    'status' => 'new',
                ];
            })->create();
        }
    }
}
