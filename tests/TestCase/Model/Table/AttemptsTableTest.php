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
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Attempts') ? [] : ['className' => 'LoginAttempts\Model\Table\AttemptsTable'];
        $this->Attempts = TableRegistry::get('Attempts', $config);
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
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
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
