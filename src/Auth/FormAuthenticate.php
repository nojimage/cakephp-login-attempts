<?php
declare(strict_types=1);

namespace LoginAttempts\Auth;

use Cake\Auth\FormAuthenticate as BaseFormAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;

/**
 * LoginAttempts Form Authenticate class
 */
class FormAuthenticate extends BaseFormAuthenticate
{
    /**
     * construct
     *
     * @param \Cake\Controller\ComponentRegistry $registry The Component registry used on this request.
     * @param array $config Array of config to use.
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_defaultConfig += [
            'attemptLimit' => 5,
            'attemptDuration' => '+5 minutes',
            'attemptAction' => 'login',
            'attemptsStorageModel' => 'LoginAttempts.Attempts',
        ];
        parent::__construct($registry, $config);
    }

    /**
     * get action name
     *
     * @return string
     */
    protected function _getAction()
    {
        return $this->getConfig('userModel') . '.' . $this->getConfig('attemptAction');
    }

    /**
     * authenticate & check attempt counts
     *
     * @param \Cake\Http\ServerRequest $request The request that contains login information.
     * @param \Cake\Http\Response $response Unused response object.
     * @return mixed False on login failure. An array of User data on success.
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        $ip = $request->clientIp();
        $action = $this->_getAction();
        $attempts = $this->getAttemptsTable();

        // check attempts
        if (!$attempts->check($ip, $action, $this->getConfig('attemptLimit'))) {
            return false;
        }

        $user = parent::authenticate($request, $response);
        if ($user) {
            // on success clear attempts
            $attempts->reset($ip, $action);
        } else {
            // on failure record attempts
            $attempts->fail($ip, $action, $this->getConfig('attemptDuration'));
        }

        return $user;
    }

    /**
     * @return \LoginAttempts\Model\Table\AttemptsTableInterface
     */
    protected function getAttemptsTable()
    {
        return TableRegistry::getTableLocator()->get($this->getConfig('attemptsStorageModel'));
    }
}
