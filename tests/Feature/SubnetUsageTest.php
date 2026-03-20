<?php

use App\Models\User;

it('shows the subnet usage placeholder page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/subnet-usage');

    $response->assertOk();
    $response->assertSee('Subnet usage across Engineering');
});

it('requires authentication', function () {
    $this->get('/subnet-usage')->assertRedirect('/login');
});
