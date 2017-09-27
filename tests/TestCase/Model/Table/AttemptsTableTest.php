<?php

namespace LoginAttempts\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use LoginAttempts\Model\Table\AttemptsTable;

/**
 * LoginAttempts\Model\Table\AttemptsTable Test Case
 */
class AttemptsTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.login_attempts.attempts'
    ];

    /**
     * @var AttemptsTable
     */
    private $Attempts;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Attempts = TableRegistry::get('Attempts', ['className' => AttemptsTable::class]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Attempts);
        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @dataProvider dataValidation
     */
    public function testValidation($field, $data, $expects)
    {
        $entity = $this->Attempts->newEntity([
            $field => $data,
        ]);

        if ($expects === true) {
            $this->assertEmpty($entity->errors($field));
        } else {
            $this->assertSame($expects, current($entity->errors($field)));
        }
    }

    /**
     * test data for testValidation
     *
     * @return array
     */
    public function dataValidation()
    {
        return [
            ['ip', null, 'This field cannot be left empty'],
            ['ip', 'invalid ip', 'invalid IP address'],
            ['ip', '192.168.1.1', true],
            ['ip', '256.1.1.1', 'invalid IP address'],
            ['action', null, 'This field cannot be left empty'],
            ['action', 'index', true],
            ['expires', null, 'This field cannot be left empty'],
            ['expires', '2017-12-31 00:00:00', true],
            ['created_at', null, 'This field cannot be left empty'],
            ['created_at', '2017-01-01 00:00:00', true],
        ];
    }

    /**
     * Test fail method
     *
     * @return void
     */
    public function testFail()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test check method
     *
     * @return void
     */
    public function testCheck()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test reset method
     *
     * @return void
     */
    public function testReset()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test cleanup method
     *
     * @return void
     */
    public function testCleanup()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
