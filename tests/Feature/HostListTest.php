<?php

use App\Models\Host;
use App\Models\User;

it('shows the host list to an authenticated user', function () {
    $user = User::factory()->create();
    $host = Host::factory()->create(['hostname' => 'glasgow-server-1', 'mac' => 'aa:bb:cc:dd:ee:ff']);

    $response = $this->actingAs($user)->get('/');

    $response->assertOk();
    $response->assertSee('glasgow-server-1');
    $response->assertSee('aa:bb:cc:dd:ee:ff');
});

it('requires authentication', function () {
    $this->get('/')->assertRedirect('/login');
});

it('shows the 50 most recently updated hosts by default', function () {
    $user = User::factory()->create();
    $oldHost = Host::factory()->create(['hostname' => 'old-host', 'last_updated' => now()->subDays(10)]);
    $newHost = Host::factory()->create(['hostname' => 'new-host', 'last_updated' => now()]);

    $response = $this->actingAs($user)->get('/');

    $content = $response->getContent();
    expect(strpos($content, 'new-host'))->toBeLessThan(strpos($content, 'old-host'));
});

it('searches across hostname, mac, owner, ip and notes', function () {
    $user = User::factory()->create();
    $matchingHost = Host::factory()->create(['hostname' => 'glasgow-lab-pc']);
    $otherHost = Host::factory()->create(['hostname' => 'edinburgh-server']);

    $response = $this->actingAs($user)->get('/?search=glasgow');

    $response->assertSee('glasgow-lab-pc');
    $response->assertDontSee('edinburgh-server');
});

it('searches for ssd keyword to find SSD hosts', function () {
    $user = User::factory()->create();
    $ssdHost = Host::factory()->withSsd()->create(['hostname' => 'ssd-host']);
    $normalHost = Host::factory()->create(['hostname' => 'normal-host', 'ssd' => 'No']);

    $response = $this->actingAs($user)->get('/?search=ssd');

    $response->assertSee('ssd-host');
    $response->assertDontSee('normal-host');
});

it('searches for disabled keyword to find disabled hosts', function () {
    $user = User::factory()->create();
    $disabledHost = Host::factory()->disabled()->create(['hostname' => 'dead-host']);
    $enabledHost = Host::factory()->create(['hostname' => 'live-host']);

    $response = $this->actingAs($user)->get('/?search=disabled');

    $response->assertSee('dead-host');
    $response->assertDontSee('live-host');
});

it('orders search results by hostname ascending', function () {
    $user = User::factory()->create();
    Host::factory()->create(['hostname' => 'charlie', 'owner' => 'test@glasgow.ac.uk']);
    Host::factory()->create(['hostname' => 'alpha', 'owner' => 'test@glasgow.ac.uk']);

    $response = $this->actingAs($user)->get('/?search=glasgow');

    $content = $response->getContent();
    expect(strpos($content, 'alpha'))->toBeLessThan(strpos($content, 'charlie'));
});

it('shows total count of entries', function () {
    $user = User::factory()->create();
    Host::factory()->count(3)->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertSee('Total: 3 entries');
});
