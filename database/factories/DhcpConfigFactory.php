<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DhcpConfigFactory extends Factory
{
    public function definition(): array
    {
        return [
            'header' => $this->faker->text(),
            'subnets' => $this->faker->text(),
            'groups' => $this->faker->text(),
            'footer' => $this->faker->text()
        ];
    }
}
