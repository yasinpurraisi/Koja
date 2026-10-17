<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Salon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'salon_id' => Salon::factory(),

            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->optional(0.9)->paragraph(),

            'status' => 'visible',
        ];
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'hidden_by_admin',
        ]);
    }
}