<?php

use App\Models\Host;

it('maps ?action=api_checkupdates to check-updates endpoint', function () {
    Host::factory()->create();

    $response = $this->get('/?action=api_checkupdates&host=dhcp1.gla.ac.uk');

    $response->assertRedirect('/api/dhcp/check-updates?host=dhcp1.gla.ac.uk');
});

it('maps ?action=api_getdhcp to config endpoint', function () {
    $response = $this->get('/?action=api_getdhcp');

    $response->assertRedirect('/api/dhcp/config');
});

it('maps ?action=api_gethosts to hosts endpoint', function () {
    $response = $this->get('/?action=api_gethosts&host=dhcp1.gla.ac.uk');

    $response->assertRedirect('/api/dhcp/hosts?host=dhcp1.gla.ac.uk');
});

it('maps ?action=api_flagerror to flag-error endpoint', function () {
    $response = $this->get('/?action=api_flagerror');

    $response->assertRedirect('/api/dhcp/flag-error');
});

it('maps ?action=api_setonline with id to online endpoint', function () {
    $response = $this->get('/?action=api_setonline&id=42');

    $response->assertRedirect('/api/dhcp/hosts/42/online');
});

it('maps ?action=api_setoffline with id to offline endpoint', function () {
    $response = $this->get('/?action=api_setoffline&id=7');

    $response->assertRedirect('/api/dhcp/hosts/7/offline');
});
