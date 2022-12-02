<?php
declare(strict_types=1);

namespace LoginAttempts\Test\TestCase\Authenticator;

use Authentication\Authenticator\ResultInterface;
use Authentication\Identifier\IdentifierInterface;
use Cake\Http\ServerRequest;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;
use LoginAttempts\Authenticator\FormAuthenticator;

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
     * @var string
     */
    private $salt;

    /**
     * @var \LoginAttempts\Model\Table\AttemptsTable
     */
    private $Attempts;

    /**
     * Sets up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->identifier = $this->getMockBuilder(IdentifierInterface::class)->getMock();
        $this->identifier
            ->method('getErrors')
            ->willReturn([]);
        $this->auth = new FormAuthenticator($this->identifier, [
            'loginUrl' => '/login',
            'userModel' => 'AuthUsers',
        ]);

        // set password
        $this->Attempts = TableRegistry::get('LoginAttempts.Attempts');

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
        FrozenTime::setTestNow();
        parent::tearDown();
    }

    /**
     * @param string $url the request url
     * @param array|null $post post data
     * @param string $remoteAddr REMOTE_ADDR env
     * @return ServerRequest
     */
    private function getRequest($url, $post, $remoteAddr = '192.168.1.11')
    {
        if (class_exists('\Laminas\Diactoros\Uri')) {
            return (new ServerRequest([
                'uri' => new \Laminas\Diactoros\Uri($url),
                'post' => $post,
            ]))->withEnv('REMOTE_ADDR', $remoteAddr);
        }
        if (class_exists('\Zend\Diactoros\Uri')) {
            return (new ServerRequest([
                'uri' => new \Zend\Diactoros\Uri($url),
                'post' => $post,
            ]))->withEnv('REMOTE_ADDR', $remoteAddr);
        }

        return (new ServerRequest([
            'url' => $url,
            'post' => $post,
        ]))->withEnv('REMOTE_ADDR', $remoteAddr);
    }

    /**
     * test Authenticate
     */
    public function testAuthenticateNotLoginUrl()
    {
        $now = FrozenTime::parse('2017-01-02 12:23:36');
        FrozenTime::setTestNow($now);

        $recordsBefore = $this->Attempts->find()->where(['ip' => '192.168.1.11', 'expires >=' => $now])->all();
        $this->assertLessThan(5, $recordsBefore->count());

        $request = $this->getRequest('/not-login', [
            'username' => 'foo',
            'password' => 'invalid',
        ]);

        $result = $this->auth->authenticate($request);
        $this->assertSame(ResultInterface::FAILURE_OTHER, $result->getStatus());

        // not created attempt record on non-login request
        $recordsAfter = $this->Attempts->find()->where(['ip' => '192.168.1.11', 'expires >=' => $now])->all();
        $this->assertSameSize($recordsBefore, $recordsAfter, 'not created attempt record on non-login request');
    }

    /**
     * test Authenticate
     */
    public function testAuthenticateCredentialsMissing()
    {
        $now = FrozenTime::parse('2017-01-02 12:23:36');
        FrozenTime::setTestNow($now);

        $recordsBefore = $this->Attempts->find()->where(['ip' => '192.168.1.11', 'expires >=' => $now])->all();
        $this->assertLessThan(5, $recordsBefore->count());

        $request = $this->getRequest('/login', null);

        $result = $this->auth->authenticate($request);
        $this->assertSame(ResultInterface::FAILURE_CREDENTIALS_MISSING, $result->getStatus());

        // not created attempt record on non-post request
        $recordsAfter = $this->Attempts->find()->where(['ip' => '192.168.1.11', 'expires >=' => $now])->all();
        $this->assertSameSize($recordsBefore, $recordsAfter, 'not created attempt record on non-post request');
    }

    /**
     * test Authenticate
     */
    public function testAuthenticateFailure()
    {
        FrozenTime::setTestNow(FrozenTime::parse('2017-01-01 12:23:34'));

        $request = $this->getRequest('/login', [
            'username' => 'foo',
            'password' => 'invalid',
        ], '192.168.1.12');

        $result = $this->auth->authenticate($request);
        $this->assertFalse($result->isValid());

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
        FrozenTime::setTestNow(FrozenTime::parse('2017-01-01 12:23:34'));

        $request = $this->getRequest('/login', [
            'username' => 'foo',
            'password' => 'password',
        ]);

        $result = $this->auth->authenticate($request);
        $this->assertFalse($result->isValid());

        // expired
        FrozenTime::setTestNow(FrozenTime::parse('2017-01-02 12:23:35'));

        $request = $this->getRequest('/login', [
            'username' => 'foo',
            'password' => 'password',
        ]);

        $user = ['id' => 1, 'username' => 'foo'];
        $this->identifier->expects($this->once())
            ->method('identify')
            ->willReturn($user);
        $result = $this->auth->authenticate($request);
        $this->assertSame($user, $result->getData());
    }

    /**
     * test Authenticate
     */
    public function testAuthenticateSuccess()
    {
        FrozenTime::setTestNow(FrozenTime::parse('2017-01-01 12:23:34'));

        $result = $this->Attempts->find()->where(['ip' => '192.168.1.22'])->all();
        $this->assertNotNull($result);
        $this->assertCount(1, $result);

        $request = $this->getRequest('/login', [
            'username' => 'foo',
            'password' => 'password',
        ], '192.168.1.22');

        $user = ['id' => 1, 'username' => 'foo'];
        $this->identifier->expects($this->once())
            ->method('identify')
            ->willReturn($user);
        $result = $this->auth->authenticate($request);
        $this->assertTrue($result->isValid());

        // created attempt record on auth failure
        $record = $this->Attempts->find()->where(['ip' => '192.168.1.2'])->all();
        $this->assertCount(0, $record, 'reset attempt record on auth success');
    }
}
