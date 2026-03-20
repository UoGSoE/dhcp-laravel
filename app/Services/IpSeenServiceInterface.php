<?php

namespace App\Services;

interface IpSeenServiceInterface
{
    /**
     * Look up a MAC address or IP in the ARP monitoring system.
     *
     * @param  string  $query  A MAC address (aa:bb:cc:dd:ee:ff) or IP address
     */
    public function lookup(string $query): ?IpSeenResult;
}
