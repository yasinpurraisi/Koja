<?php

namespace Database\Factories;

use App\Models\Salon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Salon>
 */
class SalonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'owner_id' => User::factory()->owner(),

            'name' => fake()->company() . ' Salon',
            'description' => fake()->paragraph(),
            'phone' => fake()->unique()->numerify('+1555########'),

            'address' => fake()->streetAddress(),
            'city' => fake()->city(),

            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),

            'status' => 'pending',
            'approved_by' => null,
            'reviewed_at' => null,
        ];
    }

    public function approved(?User $admin = null): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'approved_by' => $admin?->id,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(?User $admin = null): static
    {
        return $this->state(fn () => [
            'status' => 'rejected',
            'approved_by' => $admin?->id,
            'reviewed_at' => now(),
        ]);
    }

    public function suspended(?User $admin = null): static
    {
        return $this->state(fn () => [
            'status' => 'suspended',
            'approved_by' => $admin?->id,
            'reviewed_at' => now(),
        ]);
    }
}