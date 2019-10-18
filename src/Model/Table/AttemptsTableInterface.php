<?php

namespace LoginAttempts\Model\Table;

interface AttemptsTableInterface
{
    /**
     * record on login failed
     *
     * @param string $ip A request client ip.
     * @param string $action A request target action.
     * @param string $duration Duration to disable login.
     * @return bool
     */
    public function fail($ip, $action, $duration);

    /**
     * check attempts less than $limit
     *
     * @param string $ip A request client ip.
     * @param string $action A request target action.
     * @param int $limit Number of trial limitation.
     * @return bool
     */
    public function check($ip, $action, $limit);

    /**
     * reset on login success
     *
     * @param string $ip A request client ip.
     * @param string $action A request target action.
     * @return bool
     */
    public function reset($ip, $action);
}
