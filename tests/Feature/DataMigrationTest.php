<?php

it('has the dhcp:migrate-data artisan command', function () {
    $this->artisan('dhcp:migrate-data --help')
        ->assertSuccessful();
});
