<?php

namespace Database\Factories;

use App\Enums\HostStatus;
use App\Models\Host;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Host>
 */
class HostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hostname' => fake()->domainWord().'-'.fake()->numberBetween(1, 999),
            'mac' => fake()->macAddress(),
            'ip' => '130.209.'.fake()->numberBetween(0, 255).'.'.fake()->numberBetween(1, 254),
            'added_by' => fake()->randomElement(['wra1z', 'kmc2c', 'jjt1g', 'pb57m']),
            'owner' => fake()->safeEmail(),
            'added_date' => fake()->date(),
            'status' => HostStatus::Enabled,
            'ssd' => 'No',
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function disabled(): static
    {
        return $this->state(['status' => HostStatus::Disabled]);
    }

    public function withSsd(): static
    {
        return $this->state(['ssd' => 'Yes']);
    }

    public function poolHost(): static
    {
        return $this->state([
            'ip' => null,
            'hostname' => null,
        ]);
    }
}
