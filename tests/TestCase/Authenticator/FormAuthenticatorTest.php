<?php

namespace LoginAttempts\Test\TestCase\Authenticator;

use Authentication\Identifier\IdentifierInterface;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;
use LoginAttempts\Authenticator\FormAuthenticator;
use LoginAttempts\Model\Entity\Attempt;
use LoginAttempts\Model\Table\AttemptsTable;

/**
 * test for FormAuthenticator
 */
class FormAuthenticatorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.LoginAttempts.Auth\FormAuthenticate\Attempts',
    ];

    /**
     * @var IdentifierInterface
     */
    private $identifier;

    /**
     * @var FormAuthenticator
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
     * Sets up
     */
    public function setUp()
    {
        parent::setUp();
        $this->identifier = $this->getMockBuilder(IdentifierInterface::class)->getMock();
        $this->identifier
            ->method('getErrors')
            ->willReturn([]);
        $this->auth = new FormAuthenticator($this->identifier, [
            'userModel' => 'AuthUsers',
        ]);

        // set password
        $this->Attempts = TableRegistry::get('LoginAttempts.Attempts');

        $this->response = $this->getMockBuilder(Response::class)->getMock();

        $this->salt = Security::getSalt();
        Security::setSalt('DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi');
    }

    /**
     * Tears down
     */
    public function tearDown()
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
        $this->assertFalse($result->isValid());

        // created attempt record on auth failure
        $record = $this->Attempts->find()->where(['ip' => '192.168.1.12'])->first();
        /* @var $record Attempt */
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
        $this->assertFalse($result->isValid());

        // expired
        Time::setTestNow(Time::parse('2017-01-02 12:23:35'));
        $request = (new ServerRequest([
            'post' => [
                'username' => 'foo',
                'password' => 'password',
            ],
        ]))->withEnv('REMOTE_ADDR', '192.168.1.11');

        $user = ['id' => 1, 'username' => 'foo'];
        $this->identifier->expects($this->once())
            ->method('identify')
            ->willReturn($user);
        $result = $this->auth->authenticate($request, $this->response);
        $this->assertSame($user, $result->getData());
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

        $user = ['id' => 1, 'username' => 'foo'];
        $this->identifier->expects($this->once())
            ->method('identify')
            ->willReturn($user);
        $result = $this->auth->authenticate($request, $this->response);
        $this->assertTrue($result->isValid());

        // created attempt record on auth failure
        $record = $this->Attempts->find()->where(['ip' => '192.168.1.2'])->all();
        $this->assertCount(0, $record, 'reset attempt record on auth success');
    }
}
