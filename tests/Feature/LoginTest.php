<?php

namespace Tests\Feature;

use App\Livewire\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_is_redirected_when_already_logged_in(): void
    {
        $userId = Uuid::uuid4()->toString();
        $user = $this->createValidUser($userId);
        $this->actingAs($user);

        $response = $this->get('/login');
        $response->assertRedirect(route('index'));
    }

    public function test_livewire_component_is_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertSeeLivewire(Login::class);
    }

    public function test_validation_fails_when_inputs_are_invalid_or_missing(): void
    {
        $this->fakeLdapConnection();

        $response = Livewire::test(Login::class)
            ->set('guid', 'test.user')
            ->set('password', '')
            ->call('authenticate');

        // Assert the form has errors - password is required
        $response->assertHasErrors('password');
    }

    public function test_login_fails_when_credentials_are_incorrect_(): void
    {
        $this->fakeLdapConnection();

        $response = Livewire::test(Login::class)
            ->set('guid', 'test.user')
            ->set('password', 'incorrect')
            ->call('authenticate');

        $response->assertHasErrors('authentication');
    }

    public function test_login_succeeds_when_user_exists(): void
    {
        $this->fakeLdapConnection();

        $userId = Uuid::uuid4()->toString();
        $this->createValidUser($userId);

        $response = $this->setInputsAndSubmitForm();
        $response->assertHasNoErrors();
        $response->assertRedirect(route('dhcp-entries'));
    }

    private function createValidUser(string $id): User
    {
        $userData = [
            'id' => $id,
            'forenames' => 'Joe',
            'surname' => 'Bloggs',
            'email' => 'email@email.com',
            'guid' => 'test.user'
        ];

        $user = User::create($userData);
        $user->save();

        return $user;
    }

    private function setInputsAndSubmitForm(): Testable
    {
        $response = Livewire::test(Login::class)
            ->set('guid', 'test.user')
            ->set('password', 'validpassword')
            ->call('authenticate');

        return $response;
    }
}
