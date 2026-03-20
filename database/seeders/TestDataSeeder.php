<?php

namespace Database\Seeders;

use App\Models\Host;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->createUsers();
        $this->call(DhcpSectionSeeder::class);
        $this->createHosts();
    }

    private function createUsers(): array
    {
        $adminUser = User::factory()->create([
            'username' => 'admin2x',
            'email' => 'admin2x@example.test',
            'password' => bcrypt('secret'),
            'is_admin' => true,
            'forenames' => 'Jenny',
            'surname' => 'MacAdmin',
        ]);

        $standardUser = User::factory()->create([
            'username' => 'user2x',
            'email' => 'user2x@example.test',
            'password' => bcrypt('secret'),
            'is_admin' => false,
            'forenames' => 'Olivia',
            'surname' => 'McUser',
        ]);

        return [$adminUser, $standardUser];
    }

    private function createHosts(): void
    {
        // Regular hosts with fixed IPs
        Host::factory()->count(10)->create();

        // Pool hosts (no IP, hostname auto-generated)
        Host::factory()->poolHost()->count(5)->create();

        // Disabled hosts
        Host::factory()->disabled()->count(2)->create();

        // SSD hosts
        Host::factory()->withSsd()->count(2)->create();

        // Duplicate MAC pair — same device in two buildings
        $sharedMac = 'aa:bb:cc:dd:ee:ff';
        Host::factory()->create([
            'mac' => $sharedMac,
            'hostname' => 'laptop-jws-lab',
            'ip' => '130.209.240.1',
            'notes' => 'James Watt South lab',
        ]);
        Host::factory()->create([
            'mac' => $sharedMac,
            'hostname' => 'laptop-rankine-8',
            'ip' => '130.209.240.2',
            'notes' => 'Rankine building floor 8',
        ]);
    }
}
