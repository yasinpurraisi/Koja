<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->unique()->numerify('+1555########'),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'customer',
            'status' => 'active',
            'remember_token' => fake()->optional()->regexify('[A-Za-z0-9]{10}'),
        ];
    }

    public function owner(): static
    {
        return $this->state(fn () => [
            'role' => 'owner',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => 'admin',
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => [
            'status' => 'suspended',
        ]);
    }
}