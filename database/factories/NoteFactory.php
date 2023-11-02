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
        ];
    }
}
