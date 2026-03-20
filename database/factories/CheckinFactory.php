<?php

namespace Database\Factories;

use App\Models\Checkin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Checkin>
 */
class CheckinFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hostname' => fake()->domainWord().'.gla.ac.uk',
            'checked_in_at' => fake()->dateTimeBetween('-7 days'),
        ];
    }
}
