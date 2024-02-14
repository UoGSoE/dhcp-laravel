<?php

namespace App\Jobs\Helper;

interface ErrorCacheInterface
{
    public function get();

    public function add(string $message): void;

    public function delete(): void;
}
