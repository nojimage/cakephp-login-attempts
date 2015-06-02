<?php

namespace LoginAttempts\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use LoginAttempts\Model\Entity\Attempt;
use Carbon\Carbon;

/**
 * Attempts Model
 */
class AttemptsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('attempts');
        $this->displayField('ip');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created_at' => 'new',
                ]
            ],
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('ip', 'create')
            ->notEmpty('ip')
            ->add('ip', 'ip', [
                'rule' => 'ip',
                'message' => __d('login_attemts', 'invalid IP address'),
        ]);

        $validator
            ->requirePresence('action', 'create')
            ->notEmpty('action');

        $validator
            ->requirePresence('expires', 'create')
            ->notEmpty('expires');

        $validator
            ->requirePresence('created_at', 'create')
            ->notEmpty('created_at');

        return $validator;
    }

    /**
     * record on login failed
     *
     * @param string $ip
     * @param string $action
     * @param string $duration
     * @return boolean
     */
    public function fail($ip, $action, $duration)
    {
        $attempt = $this->newEntity([
            'ip' => $ip,
            'action' => $action,
            'expires' => Carbon::parse($duration),
            'created_at' => Carbon::now(),
        ]);
        return $this->save($attempt);
    }

    /**
     * check attempts less than $limit
     *
     * @param string $ip
     * @param string $action
     * @param integer $limit
     * @return boolean
     */
    public function check($ip, $action, $limit)
    {
        $count = $this->find()->where([
                'ip' => $ip,
                'action' => $action,
                'expires >=' => Carbon::now(),
            ])->count();
        return $count < $limit;
    }

    /**
     * reset on login success
     *
     * @param string $ip
     * @param string $action
     * @return boolean
     */
    public function reset($ip, $action)
    {
        return $this->deleteAll([
                'ip' => $ip,
                'action' => $action,
        ]);
    }

    /**
     * cleanup expired data
     *
     * @return boolean
     */
    public function cleanup()
    {
        return $this->deleteAll([
                'expires <' => Carbon::now(),
        ]);
    }

}
