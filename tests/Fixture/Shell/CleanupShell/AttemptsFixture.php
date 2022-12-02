<?php

namespace LoginAttempts\Test\Fixture\Shell\CleanupShell;

use LoginAttempts\Test\Fixture\AttemptsFixture as BaseFixture;

/**
 * AttemptsFixture
 *
 */
class AttemptsFixture extends BaseFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'ip' => '192.168.1.11',
            'action' => 'Users.login',
            'expires' => '2017-01-02 12:23:34',
            'created_at' => '2017-01-01 12:23:34',
        ],
    ];
}
