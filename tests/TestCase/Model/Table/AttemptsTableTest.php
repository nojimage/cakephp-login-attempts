<?php
declare(strict_types=1);

namespace LoginAttempts\Test\TestCase\Model\Table;

use Cake\I18n\DateTime;
use Cake\TestSuite\TestCase;
use LoginAttempts\Model\Entity\Attempt;
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
    public array $fixtures = [
        'plugin.LoginAttempts.Attempts',
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
    public function setUp(): void
    {
        parent::setUp();
        $this->Attempts = $this->fetchTable('Attempts', ['className' => AttemptsTable::class]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Attempts);
        DateTime::setTestNow();
        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @dataProvider dataValidation
     */
    public function testValidation($field, $data, $expects): void
    {
        $entity = $this->Attempts->newEntity([
            $field => $data,
        ]);

        if ($expects === true) {
            $this->assertEmpty($entity->getError($field));
        } else {
            $this->assertSame($expects, current($entity->getError($field)));
        }
    }

    /**
     * test data for testValidation
     *
     * @return array
     */
    public static function dataValidation(): array
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
    public function testFail(): void
    {
        DateTime::setTestNow('2017-01-01 12:23:34');
        $result = $this->Attempts->fail('192.168.1.11', 'Users.login', '+ 1days');

        $this->assertInstanceOf(Attempt::class, $result);

        // check saved
        $saved = $this->Attempts->get($result->id);
        $this->assertSame('192.168.1.11', $saved->ip);
        $this->assertSame('Users.login', $saved->action);
        $this->assertSame('2017-01-02 12:23:34', $saved->expires->format('Y-m-d H:i:s'));
    }

    /**
     * Test check method
     *
     * @return void
     */
    public function testCheck(): void
    {
        DateTime::setTestNow('2017-01-01 12:23:34');

        $result = $this->Attempts->check('192.168.1.11', 'Users.login', 1);
        $this->assertTrue($result, 'table is empty, then true');

        $this->Attempts->fail('192.168.1.11', 'Users.login', '+ 1days');
        $result = $this->Attempts->check('192.168.1.11', 'Users.login', 1);
        $this->assertFalse($result, 'has one record, then false');

        $result = $this->Attempts->check('192.168.1.11', 'Users.login', 2);
        $this->assertTrue($result, 'below limitation count');
        $result = $this->Attempts->check('192.168.1.12', 'Users.login', 1);
        $this->assertTrue($result, 'other ip access');
        $result = $this->Attempts->check('192.168.1.11', 'Administrators.login', 1);
        $this->assertTrue($result, 'other action request');
        DateTime::setTestNow('2017-01-02 12:23:34');
        $result = $this->Attempts->check('192.168.1.11', 'Users.login', 1);
        $this->assertFalse($result, 'unexpired');
        DateTime::setTestNow('2017-01-02 12:23:35');
        $result = $this->Attempts->check('192.168.1.11', 'Users.login', 1);
        $this->assertTrue($result, 'expired');
    }

    /**
     * Test reset method
     *
     * @return void
     */
    public function testReset(): void
    {
        DateTime::setTestNow('2017-01-01 12:23:34');

        $this->Attempts->fail('192.168.1.11', 'Users.login', '+ 1days');
        $this->Attempts->fail('192.168.1.12', 'Users.login', '+ 1days');

        $this->Attempts->reset('192.168.1.11', 'Users.login');

        $this->assertCount(1, $this->Attempts->find()->all());
    }

    /**
     * Test cleanup method
     *
     * @return void
     */
    public function testCleanup(): void
    {
        DateTime::setTestNow('2017-01-01 12:23:34');

        $this->Attempts->fail('192.168.1.11', 'Users.login', '+ 1days');
        $this->Attempts->fail('192.168.1.12', 'Users.login', '+ 1days');

        DateTime::setTestNow('2017-01-01 12:23:34');
        $this->Attempts->cleanup();
        $this->assertCount(2, $this->Attempts->find()->all());

        DateTime::setTestNow('2017-01-02 12:23:35');
         $this->Attempts->cleanup();
        $this->assertCount(0, $this->Attempts->find()->all(), 'cleanup expired');
    }
}
