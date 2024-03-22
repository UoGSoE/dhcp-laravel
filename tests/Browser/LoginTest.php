<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function test_a_user_with_valid_credentials_can_login_successfully(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->type('guid', 'admin')
                ->type('password', 'admin')
                ->assertSee('DHCP Entries');
        });
    }
}
