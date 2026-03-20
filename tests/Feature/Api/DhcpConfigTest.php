<?php

use App\Enums\HostStatus;
use App\Models\DhcpSection;
use App\Models\Host;

beforeEach(function () {
    DhcpSection::create(['section' => 'Header', 'body' => '# DHCP Header']);
    DhcpSection::create(['section' => 'Subnets', 'body' => '# Subnets']);
    DhcpSection::create(['section' => 'Groups', 'body' => '# Groups']);
    DhcpSection::create(['section' => 'Footer', 'body' => '# Footer']);
});

it('includes all sections in order with host lines between header and subnets', function () {
    Host::factory()->create(['hostname' => 'testhost', 'mac' => 'aa:bb:cc:dd:ee:ff', 'ip' => null, 'ssd' => 'No']);

    $response = $this->get('/api/dhcp/config');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');

    $content = $response->getContent();
    $headerPos = strpos($content, '# DHCP Header');
    $hostPos = strpos($content, 'host testhost');
    $subnetsPos = strpos($content, '# Subnets');
    $groupsPos = strpos($content, '# Groups');
    $footerPos = strpos($content, '# Footer');

    expect($headerPos)->toBeLessThan($hostPos)
        ->and($hostPos)->toBeLessThan($subnetsPos)
        ->and($subnetsPos)->toBeLessThan($groupsPos)
        ->and($groupsPos)->toBeLessThan($footerPos);
});

it('generates correct host line for enabled host with no IP', function () {
    Host::factory()->create(['hostname' => 'poolhost', 'mac' => 'aa:bb:cc:dd:ee:ff', 'ip' => null, 'ssd' => 'No']);

    $response = $this->get('/api/dhcp/config');

    $response->assertSee("\thost poolhost { hardware ethernet aa:bb:cc:dd:ee:ff; }");
});

it('generates correct host line for enabled host with fixed IP', function () {
    Host::factory()->create(['hostname' => 'fixedhost', 'mac' => '11:22:33:44:55:66', 'ip' => '130.209.240.1', 'ssd' => 'No']);

    $response = $this->get('/api/dhcp/config');

    $response->assertSee("\thost fixedhost { hardware ethernet 11:22:33:44:55:66; fixed-address 130.209.240.1; default-lease-time 86400; max-lease-time 86400; }");
});

it('generates correct host line for enabled host with SSD', function () {
    Host::factory()->create(['hostname' => 'ssdhost', 'mac' => 'aa:bb:cc:dd:ee:ff', 'ip' => null, 'ssd' => 'Yes']);

    $response = $this->get('/api/dhcp/config');

    $response->assertSee("\thost ssdhost { hardware ethernet aa:bb:cc:dd:ee:ff; option domain-name-servers 130.209.16.85, 130.209.16.177; }");
});

it('generates correct host line for disabled host', function () {
    Host::factory()->disabled()->create(['hostname' => 'deadhost', 'mac' => 'aa:bb:cc:dd:ee:ff', 'ip' => null, 'ssd' => 'No']);

    $response = $this->get('/api/dhcp/config');

    $response->assertSee("### DISABLED \thost deadhost { hardware ethernet aa:bb:cc:dd:ee:ff; }");
});

it('does not disable hosts with Down status', function () {
    Host::factory()->create(['hostname' => 'downhost', 'mac' => 'aa:bb:cc:dd:ee:ff', 'ip' => null, 'status' => HostStatus::Down]);

    $response = $this->get('/api/dhcp/config');

    $content = $response->getContent();
    expect($content)->toContain("\thost downhost { hardware ethernet aa:bb:cc:dd:ee:ff; }")
        ->and($content)->not->toContain('### DISABLED');
});

it('does not disable hosts with Up status', function () {
    Host::factory()->create(['hostname' => 'uphost', 'mac' => 'aa:bb:cc:dd:ee:ff', 'ip' => null, 'status' => HostStatus::Up]);

    $response = $this->get('/api/dhcp/config');

    $content = $response->getContent();
    expect($content)->toContain("\thost uphost { hardware ethernet aa:bb:cc:dd:ee:ff; }")
        ->and($content)->not->toContain('### DISABLED');
});

it('orders hosts by hostname ascending', function () {
    Host::factory()->create(['hostname' => 'charlie', 'mac' => '11:11:11:11:11:11']);
    Host::factory()->create(['hostname' => 'alpha', 'mac' => '22:22:22:22:22:22']);
    Host::factory()->create(['hostname' => 'bravo', 'mac' => '33:33:33:33:33:33']);

    $response = $this->get('/api/dhcp/config');

    $content = $response->getContent();
    $alphaPos = strpos($content, 'host alpha');
    $bravoPos = strpos($content, 'host bravo');
    $charliePos = strpos($content, 'host charlie');

    expect($alphaPos)->toBeLessThan($bravoPos)
        ->and($bravoPos)->toBeLessThan($charliePos);
});
