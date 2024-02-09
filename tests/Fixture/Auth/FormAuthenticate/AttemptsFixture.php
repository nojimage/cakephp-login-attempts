<?php

namespace LoginAttempts\Test\Fixture\Auth\FormAuthenticate;

use LoginAttempts\Test\Fixture\AttemptsFixture as BaseFixture;

/**
 * AttemptsFixture
 */
class AttemptsFixture extends BaseFixture
{
    /**
     * Records
     *
     * @var array
     */
    public array $records = [
        [
            'id' => 1,
            'ip' => '192.168.1.11',
            'action' => 'AuthUsers.login',
            'expires' => '2017-01-02 12:23:34',
            'created_at' => '2017-01-01 12:23:34',
        ],
        [
            'id' => 2,
            'ip' => '192.168.1.11',
            'action' => 'AuthUsers.login',
            'expires' => '2017-01-02 12:23:35',
            'created_at' => '2017-01-01 12:23:34',
        ],
        [
            'id' => 3,
            'ip' => '192.168.1.11',
            'action' => 'AuthUsers.login',
            'expires' => '2017-01-02 12:23:36',
            'created_at' => '2017-01-01 12:23:34',
        ],
        [
            'id' => 4,
            'ip' => '192.168.1.11',
            'action' => 'AuthUsers.login',
            'expires' => '2017-01-02 12:23:37',
            'created_at' => '2017-01-01 12:23:34',
        ],
        [
            'id' => 5,
            'ip' => '192.168.1.11',
            'action' => 'AuthUsers.login',
            'expires' => '2017-01-02 12:23:38',
            'created_at' => '2017-01-01 12:23:34',
        ],
        [
            'id' => 6,
            'ip' => '192.168.1.22',
            'action' => 'AuthUsers.login',
            'expires' => '2017-01-02 12:23:34',
            'created_at' => '2017-01-01 12:23:34',
        ],
    ];
}
