<?php

use App\Mail\DhcpFileCorrupt;
use Illuminate\Support\Facades\Mail;

it('sends alert email on GET request', function () {
    Mail::fake();

    $response = $this->get('/api/dhcp/flag-error');

    $response->assertOk();
    Mail::assertQueued(DhcpFileCorrupt::class);
});

it('sends alert email on POST request', function () {
    Mail::fake();

    $response = $this->post('/api/dhcp/flag-error');

    $response->assertOk();
    Mail::assertQueued(DhcpFileCorrupt::class);
});
