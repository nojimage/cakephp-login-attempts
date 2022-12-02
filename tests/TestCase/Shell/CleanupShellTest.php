<?php
declare(strict_types=1);

namespace LoginAttempts\Test\TestCase\Shell;

use Cake\Console\ConsoleIo;
use Cake\I18n\FrozenTime;
use Cake\TestSuite\TestCase;
use LoginAttempts\Model\Table\AttemptsTable;
use LoginAttempts\Shell\CleanupShell;

/**
 * LoginAttempts\Shell\CleanupShell Test Case
 */
class CleanupShellTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.LoginAttempts.Shell\CleanupShell\Attempts',
    ];

    /**
     * @var AttemptsTable
     */
    private $Attempts;

    /**
     * @var CleanupShell
     */
    private $Cleanup;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->io = $this->getMockBuilder(ConsoleIo::class)->getMock();
        $this->Cleanup = new CleanupShell($this->io);
        $this->Attempts = $this->getTableLocator()->get('Attempts', ['className' => AttemptsTable::class]);
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
        $this->Cleanup->main();
        $this->assertCount(1, $this->Attempts->find()->all());

        FrozenTime::setTestNow('2017-01-02 12:23:35');
        $this->Cleanup->main();
        $this->assertCount(0, $this->Attempts->find()->all(), 'cleanup expired');
    }
}
