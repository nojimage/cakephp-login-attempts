<?php
namespace LoginAttempts\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AttemptsFixture
 *
 */
class AttemptsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'ip' => ['type' => 'string', 'length' => 45, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'action' => ['type' => 'string', 'length' => 64, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'expires' => ['type' => 'timestamp', 'length' => null, 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        'created_at' => ['type' => 'timestamp', 'length' => null, 'null' => false, 'default' => '0000-00-00 00:00:00', 'comment' => '', 'precision' => null],
        '_indexes' => [
            'IX_ip_action' => ['type' => 'index', 'columns' => ['ip', 'action'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'ip' => 'Lorem ipsum dolor sit amet',
            'action' => 'Lorem ipsum dolor sit amet',
            'expires' => 1433235556,
            'created_at' => 1433235556
        ],
        [
            'id' => 2,
            'ip' => 'Lorem ipsum dolor sit amet',
            'action' => 'Lorem ipsum dolor sit amet',
            'expires' => 1433235556,
            'created_at' => 1433235556
        ],
        [
            'id' => 3,
            'ip' => 'Lorem ipsum dolor sit amet',
            'action' => 'Lorem ipsum dolor sit amet',
            'expires' => 1433235556,
            'created_at' => 1433235556
        ],
        [
            'id' => 4,
            'ip' => 'Lorem ipsum dolor sit amet',
            'action' => 'Lorem ipsum dolor sit amet',
            'expires' => 1433235556,
            'created_at' => 1433235556
        ],
        [
            'id' => 5,
            'ip' => 'Lorem ipsum dolor sit amet',
            'action' => 'Lorem ipsum dolor sit amet',
            'expires' => 1433235556,
            'created_at' => 1433235556
        ],
        [
            'id' => 6,
            'ip' => 'Lorem ipsum dolor sit amet',
            'action' => 'Lorem ipsum dolor sit amet',
            'expires' => 1433235556,
            'created_at' => 1433235556
        ],
        [
            'id' => 7,
            'ip' => 'Lorem ipsum dolor sit amet',
            'action' => 'Lorem ipsum dolor sit amet',
            'expires' => 1433235556,
            'created_at' => 1433235556
        ],
        [
            'id' => 8,
            'ip' => 'Lorem ipsum dolor sit amet',
            'action' => 'Lorem ipsum dolor sit amet',
            'expires' => 1433235556,
            'created_at' => 1433235556
        ],
        [
            'id' => 9,
            'ip' => 'Lorem ipsum dolor sit amet',
            'action' => 'Lorem ipsum dolor sit amet',
            'expires' => 1433235556,
            'created_at' => 1433235556
        ],
        [
            'id' => 10,
            'ip' => 'Lorem ipsum dolor sit amet',
            'action' => 'Lorem ipsum dolor sit amet',
            'expires' => 1433235556,
            'created_at' => 1433235556
        ],
    ];
}
