<?php

use App\Livewire\HostForm;
use App\Models\Checkin;
use App\Models\Host;
use App\Models\User;
use Livewire\Livewire;

it('can load a host via edit-host event', function () {
    $user = User::factory()->create();
    $host = Host::factory()->create([
        'hostname' => 'glasgow-lab-1',
        'mac' => 'aa:bb:cc:dd:ee:ff',
        'owner' => 'researcher@glasgow.ac.uk',
    ]);

    Livewire::actingAs($user)
        ->test(HostForm::class)
        ->dispatch('edit-host', id: $host->id)
        ->assertSet('mac', 'aa:bb:cc:dd:ee:ff')
        ->assertSet('owner', 'researcher@glasgow.ac.uk')
        ->assertSet('hostname', 'glasgow-lab-1');
});

it('shows Edit Host heading when editing', function () {
    $user = User::factory()->create();
    $host = Host::factory()->create();

    Livewire::actingAs($user)
        ->test(HostForm::class)
        ->dispatch('edit-host', id: $host->id)
        ->assertSee('Edit Host');
});

it('shows Create Host heading when creating', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(HostForm::class)
        ->dispatch('create-host')
        ->assertSee('Create Host');
});

it('can reset form via create-host event', function () {
    $user = User::factory()->create();
    $host = Host::factory()->create(['hostname' => 'glasgow-lab-1', 'mac' => 'aa:bb:cc:dd:ee:ff']);

    Livewire::actingAs($user)
        ->test(HostForm::class)
        ->dispatch('edit-host', id: $host->id)
        ->assertSet('mac', 'aa:bb:cc:dd:ee:ff')
        ->dispatch('create-host')
        ->assertSet('mac', '')
        ->assertSet('owner', '')
        ->assertSet('hostname', '')
        ->assertSet('status', 'Enabled')
        ->assertSet('ssd', 'No');
});

it('can create a host with valid data', function () {
    $user = User::factory()->create(['username' => 'wra1z']);

    Livewire::actingAs($user)
        ->test(HostForm::class)
        ->set('mac', 'AA:BB:CC:DD:EE:FF')
        ->set('owner', 'researcher@glasgow.ac.uk')
        ->set('ip', '130.209.240.10')
        ->set('hostname', 'glasgow-lab-1')
        ->set('status', 'Enabled')
        ->set('ssd', 'No')
        ->set('notes', 'Main lab workstation')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('host-saved');

    $host = Host::first();
    expect($host->mac)->toBe('aa:bb:cc:dd:ee:ff')
        ->and($host->owner)->toBe('researcher@glasgow.ac.uk')
        ->and($host->hostname)->toBe('glasgow-lab-1')
        ->and($host->added_by)->toBe('wra1z');
});

it('auto-generates hostname as eng-pool-{id} when blank', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(HostForm::class)
        ->set('mac', 'AA:BB:CC:DD:EE:FF')
        ->set('owner', 'test@glasgow.ac.uk')
        ->call('save')
        ->assertHasNoErrors();

    $host = Host::first();
    expect($host->hostname)->toBe("eng-pool-{$host->id}");
});

it('normalises MAC addresses to lowercase colon-separated', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(HostForm::class)
        ->set('mac', 'AABBCCDDEEFF')
        ->set('owner', 'test@glasgow.ac.uk')
        ->call('save')
        ->assertHasNoErrors();

    expect(Host::first()->mac)->toBe('aa:bb:cc:dd:ee:ff');
});

it('validates required fields and rejects invalid data', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(HostForm::class)
        ->set('mac', '')
        ->set('owner', '')
        ->call('save')
        ->assertHasErrors(['mac', 'owner']);

    expect(Host::count())->toBe(0);
});

it('validates IP must be in allowed range', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(HostForm::class)
        ->set('mac', 'AA:BB:CC:DD:EE:FF')
        ->set('owner', 'test@glasgow.ac.uk')
        ->set('ip', '192.168.1.1')
        ->call('save')
        ->assertHasErrors(['ip']);

    expect(Host::count())->toBe(0);
});

it('validates IP uniqueness across hosts', function () {
    $user = User::factory()->create();
    Host::factory()->create(['ip' => '130.209.240.10']);

    Livewire::actingAs($user)
        ->test(HostForm::class)
        ->set('mac', 'AA:BB:CC:DD:EE:FF')
        ->set('owner', 'test@glasgow.ac.uk')
        ->set('ip', '130.209.240.10')
        ->call('save')
        ->assertHasErrors(['ip']);
});

it('warns on duplicate MAC but still saves', function () {
    $user = User::factory()->create();
    Host::factory()->create(['mac' => 'aa:bb:cc:dd:ee:ff']);

    Livewire::actingAs($user)
        ->test(HostForm::class)
        ->set('mac', 'AA:BB:CC:DD:EE:FF')
        ->set('owner', 'test@glasgow.ac.uk')
        ->call('save')
        ->assertHasNoErrors();

    expect(Host::where('mac', 'aa:bb:cc:dd:ee:ff')->count())->toBe(2);
});

it('can update an existing host', function () {
    $user = User::factory()->create();
    $host = Host::factory()->create(['hostname' => 'old-name', 'mac' => 'aa:bb:cc:dd:ee:ff']);

    Livewire::actingAs($user)
        ->test(HostForm::class)
        ->dispatch('edit-host', id: $host->id)
        ->set('hostname', 'new-name')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('host-saved');

    expect($host->fresh()->hostname)->toBe('new-name');
});

it('shows delete confirmation modal when editing a host', function () {
    $user = User::factory()->create();
    $host = Host::factory()->create();

    Livewire::actingAs($user)
        ->test(HostForm::class)
        ->dispatch('edit-host', id: $host->id)
        ->assertSee('Delete host?')
        ->assertSee('This will permanently remove the host record');
});

it('can delete a host', function () {
    $user = User::factory()->create();
    $host = Host::factory()->create();

    Livewire::actingAs($user)
        ->test(HostForm::class)
        ->dispatch('edit-host', id: $host->id)
        ->call('delete')
        ->assertDispatched('host-saved');

    expect(Host::count())->toBe(0);
});

it('flushes checkins when a host is deleted', function () {
    $user = User::factory()->create();
    $host = Host::factory()->create();
    Checkin::factory()->create();

    Livewire::actingAs($user)
        ->test(HostForm::class)
        ->dispatch('edit-host', id: $host->id)
        ->call('delete');

    expect(Checkin::count())->toBe(0);
});
