<?php

use App\Models\DhcpSection;
use App\Models\Host;
use App\Services\DhcpConfigGenerator;

it('generates dhcpd.conf with correct structure', function () {
    DhcpSection::create(['section' => 'Header', 'body' => '# HEADER']);
    DhcpSection::create(['section' => 'Subnets', 'body' => '# SUBNETS']);
    DhcpSection::create(['section' => 'Groups', 'body' => '# GROUPS']);
    DhcpSection::create(['section' => 'Footer', 'body' => '# FOOTER']);

    Host::factory()->create(['hostname' => 'alpha', 'mac' => '11:11:11:11:11:11', 'ip' => null, 'ssd' => 'No']);
    Host::factory()->create(['hostname' => 'bravo', 'mac' => '22:22:22:22:22:22', 'ip' => '130.209.240.1', 'ssd' => 'No']);
    Host::factory()->create(['hostname' => 'charlie', 'mac' => '33:33:33:33:33:33', 'ip' => '130.209.240.2', 'ssd' => 'Yes']);
    Host::factory()->disabled()->create(['hostname' => 'delta', 'mac' => '44:44:44:44:44:44', 'ip' => null, 'ssd' => 'No']);

    $generator = app(DhcpConfigGenerator::class);
    $output = $generator->generate();

    $expected = implode("\n", [
        '# HEADER',
        "\thost alpha { hardware ethernet 11:11:11:11:11:11; }",
        "\thost bravo { hardware ethernet 22:22:22:22:22:22; fixed-address 130.209.240.1; default-lease-time 86400; max-lease-time 86400; }",
        "\thost charlie { hardware ethernet 33:33:33:33:33:33; fixed-address 130.209.240.2; default-lease-time 86400; max-lease-time 86400; option domain-name-servers 130.209.16.85, 130.209.16.177; }",
        "### DISABLED \thost delta { hardware ethernet 44:44:44:44:44:44; }",
        '# SUBNETS',
        '# GROUPS',
        '# FOOTER',
    ]);

    expect($output)->toBe($expected);
});

it('generates correct host count from real fixture', function () {
    // Structural check against the fixture generated from real data
    $fixture = file_get_contents(base_path('tests/fixtures/new-dhcpd.conf'));

    $enabledLines = preg_match_all('/^\thost /m', $fixture);
    $disabledLines = preg_match_all('/^### DISABLED /m', $fixture);

    expect($enabledLines)->toBe(9387)
        ->and($disabledLines)->toBe(480)
        ->and($enabledLines + $disabledLines)->toBe(9867);
});
