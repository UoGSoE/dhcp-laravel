<?php

use App\Services\IpSeenServiceInterface;

it('resolves the IpSeen service from the container', function () {
    $service = app(IpSeenServiceInterface::class);

    expect($service)->toBeInstanceOf(IpSeenServiceInterface::class);
});

it('null implementation returns null for any lookup', function () {
    $service = app(IpSeenServiceInterface::class);

    expect($service->lookup('aa:bb:cc:dd:ee:ff'))->toBeNull();
});
