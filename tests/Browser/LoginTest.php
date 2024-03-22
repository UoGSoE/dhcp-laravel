<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_a_user_with_valid_credentials_can_login_successfully(): void
    {
        $this->browse(function (Browser $browser) {
            User::factory()->create([
                'forenames' => 'Admin',
                'surname' => 'Admin',
                'email' => 'admin@localhost',
                'guid' => 'admin',
            ]);

            $browser->visit('/')
                ->type('guid', 'admin')
                ->type('password', 'admin')
                ->clickAndWaitForReload('button[type="submit"]')
                ->assertSee('DHCP Entries');
        });
    }
}
