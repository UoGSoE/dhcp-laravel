<?php

use App\Models\Checkin;
use App\Models\Host;

it('returns CSV with correct content-type and disposition header', function () {
    Host::factory()->create();

    $response = $this->get('/api/dhcp/hosts');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    $response->assertHeader('Content-Disposition', 'attachment; filename=dhcpinfo.csv');
});

it('returns rows with 7 fields per line', function () {
    Host::factory()->create(['hostname' => 'testhost', 'mac' => 'aa:bb:cc:dd:ee:ff', 'ip' => '130.209.240.1', 'owner' => 'test@glasgow.ac.uk', 'ssd' => 'No']);

    $response = $this->get('/api/dhcp/hosts');

    $lines = explode("\n", trim($response->getContent()));
    $fields = explode(',', $lines[0]);
    expect($fields)->toHaveCount(7);
});

it('does not include a header row', function () {
    Host::factory()->create(['hostname' => 'testhost']);

    $response = $this->get('/api/dhcp/hosts');

    $content = $response->getContent();
    expect($content)->not->toContain('Hostname')
        ->and($content)->toContain('testhost');
});

it('records a checkin when host param is provided', function () {
    Host::factory()->create();

    $this->get('/api/dhcp/hosts?host=dhcp1.gla.ac.uk');

    expect(Checkin::where('hostname', 'dhcp1.gla.ac.uk')->count())->toBe(1);
});
