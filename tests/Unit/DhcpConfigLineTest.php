<?php

use App\Enums\HostStatus;
use App\Models\Host;
use Tests\TestCase;

uses(TestCase::class);

it('generates correct line for enabled host with no IP and no SSD', function () {
    $host = new Host([
        'hostname' => 'poolhost',
        'mac' => 'aa:bb:cc:dd:ee:ff',
        'ip' => null,
        'ssd' => 'No',
        'status' => HostStatus::Enabled,
    ]);

    expect($host->toDhcpConfigLine())->toBe("\thost poolhost { hardware ethernet aa:bb:cc:dd:ee:ff; }");
});

it('generates correct line for enabled host with fixed IP', function () {
    $host = new Host([
        'hostname' => 'fixedhost',
        'mac' => '11:22:33:44:55:66',
        'ip' => '130.209.240.1',
        'ssd' => 'No',
        'status' => HostStatus::Enabled,
    ]);

    expect($host->toDhcpConfigLine())->toBe("\thost fixedhost { hardware ethernet 11:22:33:44:55:66; fixed-address 130.209.240.1; default-lease-time 86400; max-lease-time 86400; }");
});

it('generates correct line for enabled host with SSD', function () {
    $host = new Host([
        'hostname' => 'ssdhost',
        'mac' => 'aa:bb:cc:dd:ee:ff',
        'ip' => null,
        'ssd' => 'Yes',
        'status' => HostStatus::Enabled,
    ]);

    expect($host->toDhcpConfigLine())->toBe("\thost ssdhost { hardware ethernet aa:bb:cc:dd:ee:ff; option domain-name-servers 130.209.16.85, 130.209.16.177; }");
});

it('generates correct line for disabled host', function () {
    $host = new Host([
        'hostname' => 'deadhost',
        'mac' => 'aa:bb:cc:dd:ee:ff',
        'ip' => null,
        'ssd' => 'No',
        'status' => HostStatus::Disabled,
    ]);

    expect($host->toDhcpConfigLine())->toBe("### DISABLED \thost deadhost { hardware ethernet aa:bb:cc:dd:ee:ff; }");
});

it('does NOT mark Down status hosts as disabled', function () {
    $host = new Host([
        'hostname' => 'downhost',
        'mac' => 'aa:bb:cc:dd:ee:ff',
        'ip' => null,
        'ssd' => 'No',
        'status' => HostStatus::Down,
    ]);

    expect($host->toDhcpConfigLine())->toBe("\thost downhost { hardware ethernet aa:bb:cc:dd:ee:ff; }");
});

it('does NOT mark Up status hosts as disabled', function () {
    $host = new Host([
        'hostname' => 'uphost',
        'mac' => 'aa:bb:cc:dd:ee:ff',
        'ip' => null,
        'ssd' => 'No',
        'status' => HostStatus::Up,
    ]);

    expect($host->toDhcpConfigLine())->toBe("\thost uphost { hardware ethernet aa:bb:cc:dd:ee:ff; }");
});
