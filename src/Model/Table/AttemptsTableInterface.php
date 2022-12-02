<?php
declare(strict_types=1);

namespace LoginAttempts\Model\Table;

interface AttemptsTableInterface
{
    /**
     * record on login failed
     *
     * @param string $ip A request client ip.
     * @param string $action A request target action.
     * @param string $duration Duration to disable login.
     * @return \Cake\Datasource\EntityInterface|false
     */
    public function fail(string $ip, string $action, string $duration);

    /**
     * check attempts less than $limit
     *
     * @param string $ip A request client ip.
     * @param string $action A request target action.
     * @param int $limit Number of trial limitation.
     * @return bool
     */
    public function check(string $ip, string $action, int $limit): bool;

    /**
     * reset on login success
     *
     * @param string $ip A request client ip.
     * @param string $action A request target action.
     * @return int
     */
    public function reset(string $ip, string $action): int;
}
