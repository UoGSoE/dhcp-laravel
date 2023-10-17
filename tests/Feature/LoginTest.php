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

    public function test_login_fails_when_data_is_invalid(): void
    {
        $response = $this->from(route('authenticate'))->post(route('authenticate'), [
            'guid' => 123,
            'password' => ''
        ]);

        $response->assertSessionHasErrors([
            'guid',
            'password'
        ]);
    }

    public function test_login_succeeds_when_user_exists(): void
    {
        $userId = Uuid::uuid4()->toString();
        $user = $this->createValidUser($userId);
        $response = $this->setInputsAndSubmitForm();
        $response->assertHasNoErrors();
    }

    public function test_redirects_to_index_page_when_login_succeeds(): void
    {
        $userId = Uuid::uuid4()->toString();
        $user = $this->createValidUser($userId);
        $response = $this->setInputsAndSubmitForm();
        $response->assertRedirect('/');
    }

    public function test_throws_error_to_user_when_login_fails(): void
    {
        $response = $this->setInputsAndSubmitForm();
        $response->assertHasErrors();
    }

    private function createValidUser(string $id): User
    {
        $userData = [
            'id' => $id,
            'forenames' => 'Joe',
            'surname' => 'Bloggs',
            'password' => bcrypt('password'),
            'email' => 'email@email.com',
            'guid' => 'jb123'
        ];

        $user = User::create($userData);
        $user->save();

        return $user;
    }

    private function setInputsAndSubmitForm(): Testable
    {
        $response = Livewire::test(Login::class)
            ->set('guid', 'jb123')
            ->set('password', 'password')
            ->call('authenticate');

        return $response;
    }
}
