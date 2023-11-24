<?php

namespace Database\Factories;

use App\Models\DhcpEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'note' => $this->faker->sentence(),
            'created_by' => $this->faker->name(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-6 months'),
            'updated_at' => $this->faker->dateTimeBetween('-5 months', 'now'),
        ];
    }
}
