<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_succeeds(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
