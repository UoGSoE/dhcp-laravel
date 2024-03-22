<?php

namespace Database\Seeders;

use App\Models\DhcpEntry;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'forenames' => 'Admin',
            'surname' => 'Admin',
            'email' => 'admin@localhost',
            'guid' => 'admin',
        ]);

        DhcpEntry::factory(10)->create();
    }
}
