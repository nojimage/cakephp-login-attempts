<?php
declare(strict_types=1);

namespace LoginAttempts\Test\TestCase\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\I18n\DateTime;
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
     * @var Arguments
     */
    private Arguments $args;

    /**
     * @var ConsoleIo
     */
    private ConsoleIo $io;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->io = $this->createStub(ConsoleIo::class);
        $this->args = $this->createStub(Arguments::class);
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
        DateTime::setTestNow('2017-01-01 12:23:34');
        $this->Cleanup->execute($this->args, $this->io);
        $this->assertCount(1, $this->Attempts->find()->all());

        DateTime::setTestNow('2017-01-02 12:23:35');
        $this->Cleanup->execute($this->args, $this->io);
        $this->assertCount(0, $this->Attempts->find()->all(), 'cleanup expired');
    }
}
