<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ohffs\Ldap\LdapService;

class LoginController extends Controller
{
    public function __construct(
        protected LdapService $ldapService
    ) {
    }

    public function login()
    {
        return view('login');
    }

    public function authenticate(Request $request)
    {
        // dump($this->ldapService->authenticate('jb123', 'password'));
    }
}
