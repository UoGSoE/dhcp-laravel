<?php

use App\Models\Checkin;
use App\Models\Host;

it('returns Yes when no prior checkins exist for the host', function () {
    Host::factory()->create();

    $response = $this->get('/api/dhcp/check-updates?host=dhcp1.gla.ac.uk');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    expect($response->getContent())->toBe('Yes');
});

it('returns No when checkin is newer than last host update', function () {
    Host::factory()->create(['last_updated' => now()->subHour()]);
    Checkin::factory()->create([
        'hostname' => 'dhcp1.gla.ac.uk',
        'checked_in_at' => now(),
    ]);

    $response = $this->get('/api/dhcp/check-updates?host=dhcp1.gla.ac.uk');

    expect($response->getContent())->toBe('No');
});

it('returns Yes when a host has been updated since last checkin', function () {
    Checkin::factory()->create([
        'hostname' => 'dhcp1.gla.ac.uk',
        'checked_in_at' => now()->subHour(),
    ]);
    Host::factory()->create(['last_updated' => now()]);

    $response = $this->get('/api/dhcp/check-updates?host=dhcp1.gla.ac.uk');

    expect($response->getContent())->toBe('Yes');
});

it('records a checkin after each request', function () {
    Host::factory()->create();

    $this->get('/api/dhcp/check-updates?host=dhcp1.gla.ac.uk');

    expect(Checkin::where('hostname', 'dhcp1.gla.ac.uk')->count())->toBe(1);
});
