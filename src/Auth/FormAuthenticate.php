<?php

namespace LoginAttempts\Auth;

use Cake\Auth\FormAuthenticate as BaseFormAuthenticate;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\TableRegistry;
use LoginAttempts\Model\Table\AttemptsTable;

class FormAuthenticate extends BaseFormAuthenticate
{

    public function __construct(\Cake\Controller\ComponentRegistry $registry, array $config = array())
    {
        $this->_defaultConfig += [
            'attemptLimit' => 5,
            'attemptDuration' => '+5 minutes',
            'attemptAction' => 'login',
        ];
        parent::__construct($registry, $config);
    }

    protected function _getAction()
    {
        return $this->config('userModel') . '.' . $this->config('attemptAction');
    }

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
