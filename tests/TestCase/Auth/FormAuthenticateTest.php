<?php
declare(strict_types=1);

namespace LoginAttempts\Test\TestCase\Auth;

use Cake\Controller\ComponentRegistry;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;
use LoginAttempts\Auth\FormAuthenticate;

/**
 * test for FormAuthenticate
 */
class FormAuthenticateTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.LoginAttempts.AuthUsers',
        'plugin.LoginAttempts.Auth\FormAuthenticate\Attempts',
    ];

    /**
     * @var ComponentRegistry
     */
    private $Collection;

    /**
     * @var FormAuthenticate
     */
    private $auth;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var \LoginAttempts\Model\Table\AttemptsTable
     */
    private $Attempts;

    /**
     * @var \Cake\ORM\Table
     */
    private $Users;

    /**
     * Sets up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Collection = $this->getMockBuilder(ComponentRegistry::class)->getMock();
        $this->auth = new FormAuthenticate($this->Collection, [
            'userModel' => 'AuthUsers',
        ]);

        // set password
        $this->Users = TableRegistry::get('AuthUsers');
        $this->Attempts = TableRegistry::get('LoginAttempts.Attempts');

        $this->response = $this->getMockBuilder(Response::class)->getMock();

        $this->salt = Security::getSalt();
        Security::setSalt('DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi');
    }

    /**
     * Tears down
     */
    public function tearDown(): void
    {
        unset($this->auth, $this->Users, $this->Attempts);
        Security::setSalt($this->salt);
        Time::setTestNow();
        parent::tearDown();
    }

    /**
     * test Authenticate
     */
    public function testAuthenticateFailure()
    {
        Time::setTestNow(Time::parse('2017-01-01 12:23:34'));

        $request = (new ServerRequest([
            'post' => [
                'username' => 'foo',
                'password' => 'invalid',
            ],
        ]))->withEnv('REMOTE_ADDR', '192.168.1.12');

        $result = $this->auth->authenticate($request, $this->response);
        $this->assertFalse($result);

        // created attempt record on auth failure
        $record = $this->Attempts->find()->where(['ip' => '192.168.1.12'])->first();
        /** @var \LoginAttempts\Model\Entity\Attempt $record */
        $this->assertNotEmpty($record, 'created attempt record on auth failure');

        $this->assertSame('192.168.1.12', $record->ip);
        $this->assertSame('AuthUsers.login', $record->action);
        $this->assertSame('2017-01-01 12:28:34', $record->expires->format('Y-m-d H:i:s'));
    }

    /**
     * test Authenticate
     */
    public function testAuthenticateLimitAttempts()
    {
        Time::setTestNow(Time::parse('2017-01-01 12:23:34'));

        $request = (new ServerRequest([
            'post' => [
                'username' => 'foo',
                'password' => 'password',
            ],
        ]))->withEnv('REMOTE_ADDR', '192.168.1.11');

        $result = $this->auth->authenticate($request, $this->response);
        $this->assertFalse($result);

        // expired
        Time::setTestNow(Time::parse('2017-01-02 12:23:35'));
        $request = (new ServerRequest([
            'post' => [
                'username' => 'foo',
                'password' => 'password',
            ],
        ]))->withEnv('REMOTE_ADDR', '192.168.1.11');

        $result = $this->auth->authenticate($request, $this->response);
        $this->assertSame(['id' => 1, 'username' => 'foo'], $result);
    }

    /**
     * test Authenticate
     */
    public function testAuthenticateSuccess()
    {
        Time::setTestNow(Time::parse('2017-01-01 12:23:34'));

        $result = $this->Attempts->find()->where(['ip' => '192.168.1.22'])->all();
        $this->assertNotNull($result);
        $this->assertCount(1, $result);
        $request = new ServerRequest([
            'post' => [
                'username' => 'foo',
                'password' => 'password',
            ],
        ]);
        $request = $request->withEnv('REMOTE_ADDR', '192.168.1.22');

        $result = $this->auth->authenticate($request, $this->response);
        $this->assertNotEmpty($result);

        // created attempt record on auth failure
        $record = $this->Attempts->find()->where(['ip' => '192.168.1.2'])->all();
        $this->assertCount(0, $record, 'reset attempt record on auth success');
    }
}
