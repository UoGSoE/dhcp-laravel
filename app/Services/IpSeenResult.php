<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class IpSeenResult
{
    public function __construct(
        public readonly string $query,
        public readonly ?string $mac,
        public readonly ?string $ip,
        public readonly ?Carbon $lastSeen,
    ) {}
}
