<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Shipping;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Cart::all()->groupBy('user_id') as $carts) {
            $shipping = Shipping::inRandomOrder()->first();

            $order = Order::factory()->create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => $carts->first()->user_id,
                'sub_total' => $carts->sum('amount'),
                'shipping_id' => $shipping->id,
                'total_amount' => $carts->sum('amount') + $shipping->price,
                'quantity' => $carts->sum('quantity'),
                'payment_method' => 'cod',
                'payment_status' => 'paid',
                'status' => 'delivered',
                'name' => $carts[0]->user->name,
                'email' => $carts[0]->user->email,
                'phone' => '0123456789',
                'country' => 'VN',
                'address1' => 'ABC'
            ]);

            foreach ($carts as $cart) {
                $cart->update(['order_id' => $order->id]);
            }
        }
    }
}
