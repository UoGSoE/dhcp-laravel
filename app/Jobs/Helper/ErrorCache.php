<?php

namespace App\Jobs\Helper;

use Illuminate\Support\Facades\Redis;

class ErrorCache
{
    public function __construct(
        public string $cacheKey
    )
    {
    }

    public function get()
    {
        return Redis::sMembers($this->cacheKey);
    }

    public function add(string $message): void
    {
        Redis::sAdd($this->cacheKey, $message);
    }

    public function delete(): void
    {
        Redis::del($this->cacheKey);
    }
}
