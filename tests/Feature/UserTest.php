<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created(): void
    {
        $userData = [
            'forenames' => 'Joe',
            'surname' => 'Bloggs',
            'email' => 'email@email.com',
            'guid' => 'jb123'
        ];

        $user = User::create($userData);
        $userId = $user->id;

        $this->assertDatabaseHas('users', $userData);

        $retrievedUser = User::find($userId);
        $this->assertEquals($userData['guid'], $retrievedUser->guid);
    }
}
