<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomepageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_are_redirected()
    {
        $this->get(route('index'))->assertRedirect(route('login'));
    }

    // public function test_only_authenticated_users_can_access_homepage()
    // {
    // }

}
