<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => $this->faker->uuid(),
            'summary' => $this->faker->realText(255),
            'description' => $this->faker->realText(255),
            'stock' => $this->faker->randomNumber(4),
            'size' => $this->faker->randomElement(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
            'condition' => $this->faker->randomElement(['default', 'new', 'hot']),
            'status' => 'active',
            'price' => $this->faker->randomNumber(4) * 100_000,
            'discount' => rand(0, 16) * 5,
        ];
    }
}
