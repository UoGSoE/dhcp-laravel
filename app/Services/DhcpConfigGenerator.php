<?php

namespace App\Services;

use App\Models\DhcpSection;
use App\Models\Host;

class DhcpConfigGenerator
{
    public function generate(): string
    {
        $sections = DhcpSection::all()->keyBy('section');
        $hosts = Host::orderBy('hostname')->get();

        $parts = [];

        $parts[] = $sections->get('Header')?->body ?? '';

        foreach ($hosts as $host) {
            $parts[] = $host->toDhcpConfigLine();
        }

        $parts[] = $sections->get('Subnets')?->body ?? '';
        $parts[] = $sections->get('Groups')?->body ?? '';
        $parts[] = $sections->get('Footer')?->body ?? '';

        return implode("\n", $parts);
    }
}
