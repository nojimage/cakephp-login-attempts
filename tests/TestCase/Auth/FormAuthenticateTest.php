<?php

namespace LoginAttempts\Test\TestCase\Auth;

use Cake\Controller\ComponentRegistry;
use Cake\I18n\Time;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;
use LoginAttempts\Auth\FormAuthenticate;
use LoginAttempts\Model\Table\AttemptsTable;

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
     *
     * @var AttemptsTable
     */
    private $Attempts;

    /**
     *
     * @var Table
     */
    private $Users;

    /**
     * Sets up
     */
    public function setUp()
    {
        parent::setUp();
        $this->Collection = $this->getMockBuilder(ComponentRegistry::class)->getMock();
        $this->auth = new FormAuthenticate($this->Collection, [
            'userModel' => 'AuthUsers'
        ]);
        $password = password_hash('password', PASSWORD_DEFAULT);

        TableRegistry::clear();
        // set password
        $this->Users = TableRegistry::get('AuthUsers');
        $this->Users->updateAll(['password' => $password], []);

        $this->Attempts = TableRegistry::get('LoginAttempts.Attempts');

        $this->response = $this->getMockBuilder(Response::class)->getMock();

        $this->salt = Security::salt();
        Security::salt('DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi');
    }

    /**
     * Tears down
     */
    public function tearDown()
    {
        unset($this->auth);
        unset($this->Users);
        unset($this->Attempts);
        Security::salt($this->salt);
        Time::setTestNow();
        parent::tearDown();
    }

    /**
     * test Authenticate
     */
    public function testAuthenticateFailure()
    {
        Time::setTestNow('2017-01-01 12:23:34');

        $request = new Request(['post' => [
                'username' => 'foo',
                'password' => 'invalid',
        ]]);
        $request->env('REMOTE_ADDR', '192.168.1.12');

        $result = $this->auth->authenticate($request, $this->response);
        $this->assertFalse($result);

        // created attempt record on auth failure
        $record = $this->Attempts->find()->where(['ip' => '192.168.1.12'])->first();
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
        Time::setTestNow('2017-01-01 12:23:34');

        $request = new Request(['post' => [
                'username' => 'foo',
                'password' => 'password',
        ]]);
        $request->env('REMOTE_ADDR', '192.168.1.11');

        $result = $this->auth->authenticate($request, $this->response);
        $this->assertFalse($result);

        // expired
        Time::setTestNow('2017-01-02 12:23:35');
        $request = new Request(['post' => [
                'username' => 'foo',
                'password' => 'password',
        ]]);
        $request->env('REMOTE_ADDR', '192.168.1.11');

        $result = $this->auth->authenticate($request, $this->response);
        $this->assertSame(['id' => 1, 'username' => 'foo'], $result);
    }

    /**
     * test Authenticate
     */
    public function testAuthenticateSuccess()
    {
        Time::setTestNow('2017-01-01 12:23:34');

        $this->assertCount(1, $this->Attempts->find()->where(['ip' => '192.168.1.22'])->all());
        $request = new Request(['post' => [
                'username' => 'foo',
                'password' => 'password',
        ]]);
        $request->env('REMOTE_ADDR', '192.168.1.22');

        $result = $this->auth->authenticate($request, $this->response);
        $this->assertNotEmpty($result);

        // created attempt record on auth failure
        $record = $this->Attempts->find()->where(['ip' => '192.168.1.2'])->all();
        $this->assertCount(0, $record, 'reset attempt record on auth success');
    }
}
