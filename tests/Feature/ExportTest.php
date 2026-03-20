<?php

use App\Models\Host;
use App\Models\User;

it('exports CSV with header row and all hosts', function () {
    $user = User::factory()->create();
    Host::factory()->create(['hostname' => 'glasgow-pc', 'mac' => 'aa:bb:cc:dd:ee:ff', 'ip' => '130.209.240.1', 'owner' => 'test@glasgow.ac.uk']);

    $response = $this->actingAs($user)->get('/export/csv');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    $response->assertSee('glasgow-pc');
    $response->assertSee('aa:bb:cc:dd:ee:ff');
});

it('exports CSV requires authentication', function () {
    $this->get('/export/csv')->assertRedirect('/login');
});

it('exports JSON with correct structure', function () {
    $user = User::factory()->create();
    Host::factory()->create(['hostname' => 'glasgow-pc']);

    $response = $this->actingAs($user)->get('/export/json');

    $response->assertOk();
    $response->assertJson(['Message' => 'OK', 'Status' => 1]);
    $response->assertJsonCount(1, 'Data');
    $response->assertJsonPath('Data.0.hostname', 'glasgow-pc');
});

it('exports JSON requires authentication', function () {
    $this->get('/export/json')->assertRedirect('/login');
});
