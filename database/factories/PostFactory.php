<?php

namespace Database\Factories;

use App\Models\PostCategory;
use App\Models\PostTag;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        $time = Carbon::now()->subDays($this->faker->numberBetween(10, 900));

        return [
            'title' => 'Post Title ' . rand(1, 1000),
            'slug' => $this->faker->uuid(),
            'summary' => $this->faker->sentence(12),
            'description' => $this->faker->sentence(60),
            'quote' => $this->faker->sentence(16),
            'status' => 'active',
            'added_by' => User::where('role', 'admin')->inRandomOrder()->first()->id,
            'post_cat_id' => PostCategory::inRandomOrder()->first()->id,
            'post_tag_id' => PostTag::inRandomOrder()->first()->id,
            'created_at' => $time,
            'updated_at' => $time,
        ];
    }
}
