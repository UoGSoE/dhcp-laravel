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
        // protected LdapService $ldapService
    ) {
    }

    public function render()
    {
        if (Auth::check()) {
            $this->redirect(route('index'));
        }

        return view('livewire.login');
        // ->extends('layouts.app')
        // ->section('content');
    }

    public function authenticate()
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

        Auth::login($user, $this->rememberMe);
        $this->redirect(route('dhcp-entries'));
    }
}
