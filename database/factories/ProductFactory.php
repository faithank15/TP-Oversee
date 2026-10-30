<?php

namespace Database\Factories;

use App\Enums\Unit;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
            'user_id' => User::factory(),
            'category_id' => fn (array $attributes) => Category::factory()->create(['user_id' => $attributes['user_id']])->id,
            'reference' => fake()->unique()->bothify('???-####'),
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'unit' => fake()->randomElement(Unit::cases()),
            'price' => fake()->numberBetween(100, 20000),
            'quantity' => 0,
            'alert_threshold' => 5,
        ];
    }
}
