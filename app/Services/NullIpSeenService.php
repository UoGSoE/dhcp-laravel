<?php

namespace App\Services;

class NullIpSeenService implements IpSeenServiceInterface
{
    public function lookup(string $query): ?IpSeenResult
    {
        return null;
    }
}
