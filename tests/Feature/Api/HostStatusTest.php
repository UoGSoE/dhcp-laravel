<?php

use App\Enums\HostStatus;
use App\Models\Host;

it('sets status to Up via online endpoint', function () {
    $host = Host::factory()->create();

    $response = $this->postJson("/api/dhcp/hosts/{$host->id}/online");

    $response->assertOk();
    expect($host->fresh()->status)->toBe(HostStatus::Up);
});

it('sets status to Down via offline endpoint', function () {
    $host = Host::factory()->create();

    $response = $this->postJson("/api/dhcp/hosts/{$host->id}/offline");

    $response->assertOk();
    expect($host->fresh()->status)->toBe(HostStatus::Down);
});

it('returns JSON with Message OK and Status 1', function () {
    $host = Host::factory()->create();

    $response = $this->postJson("/api/dhcp/hosts/{$host->id}/online");

    $response->assertJson(['Message' => 'OK', 'Status' => 1]);
});

it('returns 404 for non-existent host', function () {
    $this->postJson('/api/dhcp/hosts/99999/online')->assertNotFound();
    $this->postJson('/api/dhcp/hosts/99999/offline')->assertNotFound();
});

it('is idempotent — calling online twice does not error', function () {
    $host = Host::factory()->create();

    $this->postJson("/api/dhcp/hosts/{$host->id}/online")->assertOk();
    $this->postJson("/api/dhcp/hosts/{$host->id}/online")->assertOk();

    expect($host->fresh()->status)->toBe(HostStatus::Up);
});
