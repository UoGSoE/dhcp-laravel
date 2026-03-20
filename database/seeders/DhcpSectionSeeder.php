<?php

namespace Database\Seeders;

use App\Models\DhcpSection;
use Illuminate\Database\Seeder;

class DhcpSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = ['Header', 'Subnets', 'Groups', 'Footer'];

        foreach ($sections as $section) {
            DhcpSection::firstOrCreate(
                ['section' => $section],
                ['body' => ''],
            );
        }
    }
}
