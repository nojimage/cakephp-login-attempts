<?php

namespace LoginAttempts\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AuthUsersFixture
 */
class AuthUsersFixture extends TestFixture
{
    public function init(): void
    {
        parent::init();
        $this->records = [];
        $this->records[] = [
            'id' => 1,
            'username' => 'foo',
            'password' => password_hash('password', PASSWORD_DEFAULT),
        ];
        $this->records[] = [
            'id' => 2,
            'username' => 'bar',
            'password' => password_hash('password', PASSWORD_DEFAULT),
        ];
        $this->records[] = [
            'id' => 3,
            'username' => 'boo',
            'password' => password_hash('password', PASSWORD_DEFAULT),
        ];
    }
}
