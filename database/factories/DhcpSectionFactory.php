<?php

namespace Database\Factories;

use App\Models\DhcpSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DhcpSection>
 */
class DhcpSectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'section' => fake()->randomElement(['Header', 'Subnets', 'Groups', 'Footer']),
            'body' => fake()->paragraph(),
        ];
    }
}
