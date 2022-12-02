<?php

namespace LoginAttempts\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AuthUsersFixture
 */
class AuthUsersFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'username' => ['type' => 'string', 'length' => 190, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'password' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'U_username' => ['type' => 'unique', 'columns' => ['username'], 'length' => []],
        ],
    ];
    // @codingStandardsIgnoreEnd

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
