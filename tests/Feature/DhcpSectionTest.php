<?php

use App\Livewire\DhcpSectionEditor;
use App\Models\Checkin;
use App\Models\DhcpSection;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    DhcpSection::create(['section' => 'Header', 'body' => '# Old header']);
});

it('allows a DHCP admin to view the section editor', function () {
    $user = User::factory()->create(['username' => 'wra1z']);
    config(['dhcp.admin_guids' => ['wra1z']]);

    $response = $this->actingAs($user)->get('/dhcp-sections/Header/edit');

    $response->assertOk();
    $response->assertSee('Header');
});

it('denies non-admin users access to the section editor', function () {
    $user = User::factory()->create(['username' => 'edinburgh-spy']);
    config(['dhcp.admin_guids' => ['wra1z']]);

    $this->actingAs($user)->get('/dhcp-sections/Header/edit')->assertForbidden();
});

it('saves updated section body', function () {
    $user = User::factory()->create(['username' => 'wra1z']);
    config(['dhcp.admin_guids' => ['wra1z']]);

    Livewire::actingAs($user)
        ->test(DhcpSectionEditor::class, ['sectionName' => 'Header'])
        ->set('body', '# New header content')
        ->call('save')
        ->assertHasNoErrors();

    expect(DhcpSection::where('section', 'Header')->first()->body)->toBe('# New header content');
});

it('flushes all checkins when a section is saved', function () {
    $user = User::factory()->create(['username' => 'wra1z']);
    config(['dhcp.admin_guids' => ['wra1z']]);
    Checkin::factory()->count(3)->create();

    Livewire::actingAs($user)
        ->test(DhcpSectionEditor::class, ['sectionName' => 'Header'])
        ->set('body', '# Updated')
        ->call('save');

    expect(Checkin::count())->toBe(0);
});
