<?php
declare(strict_types=1);

namespace LoginAttempts\Test\TestCase\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\I18n\FrozenTime;
use Cake\TestSuite\TestCase;
use LoginAttempts\Command\CleanupCommand;
use LoginAttempts\Model\Table\AttemptsTable;

/**
 * LoginAttempts\Command\CleanupCommand Test Case
 */
class CleanupCommandTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public array $fixtures = [
        'plugin.LoginAttempts.Command\CleanupCommand\Attempts',
    ];

    /**
     * @var AttemptsTable
     */
    private AttemptsTable $Attempts;

    /**
     * @var CleanupCommand
     */
    private CleanupCommand $Cleanup;

    /**
     * @var Arguments&\PHPUnit\Framework\MockObject\MockObject|\PHPUnit\Framework\MockObject\MockObject
     */
    private $args;

    /**
     * @var ConsoleIo&\PHPUnit\Framework\MockObject\MockObject|\PHPUnit\Framework\MockObject\MockObject
     */
    private $io;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->io = $this->getMockBuilder(ConsoleIo::class)->getMock();
        $this->args = $this->getMockBuilder(Arguments::class)->disableOriginalConstructor()->getMock();
        $this->Cleanup = new CleanupCommand();
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->Attempts = $this->fetchTable('Attempts', ['className' => AttemptsTable::class]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Cleanup, $this->Attempts);

        parent::tearDown();
    }

    /**
     * Test main method
     *
     * @return void
     */
    public function testMain(): void
    {
        FrozenTime::setTestNow('2017-01-01 12:23:34');
        $this->Cleanup->execute($this->args, $this->io);
        $this->assertCount(1, $this->Attempts->find()->all());

        FrozenTime::setTestNow('2017-01-02 12:23:35');
        $this->Cleanup->execute($this->args, $this->io);
        $this->assertCount(0, $this->Attempts->find()->all(), 'cleanup expired');
    }
}
