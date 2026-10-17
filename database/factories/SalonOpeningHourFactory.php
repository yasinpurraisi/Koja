<?php

namespace Database\Factories;

use App\Models\Salon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\SalonOpeningHour>
 */
class SalonOpeningHourFactory extends Factory
{
    public function definition(): array
    {
        $isClosed = fake()->boolean(10);

        return [
            'salon_id' => Salon::factory(),

            'day_of_week' => fake()->numberBetween(1, 7),

            'opens_at' => $isClosed ? null : '09:00:00',
            'closes_at' => $isClosed ? null : '18:00:00',

            'is_closed' => $isClosed,
        ];
    }
}