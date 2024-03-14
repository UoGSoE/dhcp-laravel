<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_are_redirected(): void
    {
        $response = $this->get(route('index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_access_the_homepage(): void
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get(route('index'));
        $response->assertStatus(200);
    }

}
