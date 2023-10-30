<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Ohffs\Ldap\LdapConnectionInterface;
use Ohffs\Ldap\LdapUser;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function fakeLdapConnection()
    {
        $this->instance(
            LdapConnectionInterface::class,
            new ImportLdapConnectionFake()
        );
    }
}

class ImportLdapConnectionFake implements LdapConnectionInterface
{
    public function authenticate(string $username, string $password)
    {
        if ($username === 'test.user' && $password === 'validpassword') {
            return true;
        }

        return false;
    }

    public function findUser(string $username)
    {
        return match($username) {
            'testUser' => new LdapUser([
                0 => [
                    'uid' => ['test.user'],
                    'mail' => ['test.user@example.com'],
                    'sn' => ['User'],
                    'givenname' => ['Test'],
                    'telephonenumber' => ['1234567890'],
                ]
            ]),
            'duplicateEmail' => new LdapUser([
                0 => [
                    'uid' => ['duplicate.email'],
                    'mail' => ['testemail@example.com'],
                    'sn' => ['Email'],
                    'givenname' => ['Duplicate'],
                    'telephonenumber' => ['1234567890'],
                ]
            ]),
            'missingEmail' => new LdapUser([
                0 => [
                    'uid' => ['missing.email'],
                    'sn' => ['Email'],
                    'mail',
                    'givenname' => ['Missing'],
                    'telephonenumber' => ['1234567890'],
                ]
            ]),
            'missingUser' => null,
            default => null
        };
    }

    public function findUserByEmail(string $email)
    {
        return match($email) {
            'fred.smith@example.com' => new LdapUser([
                0 => [
                    'uid' => ['fred.smith'],
                    'mail' => ['fred.smith@example.com'],
                    'sn' => ['Smith'],
                    'givenname' => ['Fred'],
                    'telephonenumber' => ['1234567890'],
                ],
            ]),
            'penny.lane@example.com' => new LdapUser([
                0 => [
                    'uid' => ['penny.lane'],
                    'mail' => ['penny.lane@example.com'],
                    'sn' => ['Lane'],
                    'givenname' => ['Penny'],
                    'telephonenumber' => ['0987654321'],
                ],
            ]),
            'mario.cart@example.com' => new LdapUser([
                0 => [
                    'uid' => ['mario.cart'],
                    'mail' => ['mario.cart@example.com'],
                    'sn' => ['Cart'],
                    'givenname' => ['Mario'],
                    'telephonenumber' => ['1234567890'],
                ],
            ]),
            'julia.smith@example.com' => new LdapUser([
                0 => [
                    'uid' => ['julia.smith'],
                    'mail' => ['julia.smith@example.com'],
                    'sn' => ['Smith'],
                    'givenname' => ['Julia'],
                    'telephonenumber' => ['0987654321'],
                ],
            ]),
            default => null
        };
    }
}
