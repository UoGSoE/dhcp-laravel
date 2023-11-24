<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DhcpEntryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'hostname' => $this->faker->unique()->word(),
            'mac_address' => $this->faker->unique()->macAddress(),
            'ip_address' => $this->faker->unique()->ipv4(),
            'owner' => $this->faker->email(),
            'added_by' => $this->faker->name(),
            'is_ssd' => $this->faker->boolean(),
            'is_active' => $this->faker->boolean(),
            'is_imported' => $this->faker->boolean(),
        ];
    }
}
