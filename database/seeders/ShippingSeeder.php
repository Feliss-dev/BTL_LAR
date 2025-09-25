<?php

namespace Database\Seeders;

use App\Models\Shipping;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shipping::factory()->createMany([
            [
                'type' => 'Thường',
                'price' => '10000',
            ],
            [
                'type' => 'Hỏa tốc',
                'price' => '30000',
            ]
        ]);
    }
}
