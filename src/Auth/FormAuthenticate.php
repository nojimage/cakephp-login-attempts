<?php

namespace LoginAttempts\Auth;

use Cake\Auth\FormAuthenticate as BaseFormAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\TableRegistry;
use LoginAttempts\Model\Table\AttemptsTable;

/**
 * LoginAttempts Form Authenticate class
 */
class FormAuthenticate extends BaseFormAuthenticate
{

    /**
     * construct
     *
     * @param ComponentRegistry $registry The Component registry used on this request.
     * @param array $config Array of config to use.
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_defaultConfig += [
            'attemptLimit' => 5,
            'attemptDuration' => '+5 minutes',
            'attemptAction' => 'login',
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
        return $this->config('userModel') . '.' . $this->config('attemptAction');
    }

    /**
     * authenticate & check attempt counts
     *
     * @param Request $request The request that contains login information.
     * @param Response $response Unused response object.
     * @return mixed False on login failure. An array of User data on success.
     */
    public function authenticate(Request $request, Response $response)
    {
        $ip = $request->clientIp();
        $action = $this->_getAction();
        $attemtps = TableRegistry::get('LoginAttempts.Attempts');
        /* @var $attemtps AttemptsTable */

        // check attempts
        if (!$attemtps->check($ip, $action, $this->config('attemptLimit'))) {
            return false;
        }

        $user = parent::authenticate($request, $response);
        if ($user) {
            // on success clear attempts
            $attemtps->reset($ip, $action);
        } else {
            // on failure record attempts
            $attemtps->fail($ip, $action, $this->config('attemptDuration'));
        }

        return $user;
    }
}
