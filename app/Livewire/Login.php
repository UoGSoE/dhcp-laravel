<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Ohffs\Ldap\LdapService;

class Login extends Component
{
    public string $guid = '';
    public string $password = '';
    public bool $rememberMe = false;

    public function __construct(
    ) {
    }

    public function render()
    {
        if (Auth::check()) {
            $this->redirect(route('index'));
        }

        return view('livewire.login');
    }

    public function authenticate(LdapService $ldapService): void
    {
        $this->validate([
            'guid' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('guid', $this->guid)->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'guid' => 'Invalid GUID'
            ]);

            return;
        }

        if (!$ldapService->authenticate($this->guid, $this->password)) {
            throw ValidationException::withMessages([
                'authentication' => 'Authentication failed'
            ]);
            return;
        }

        Auth::login($user, $this->rememberMe);
        $this->redirect(route('dhcp-entries'));
    }
}
