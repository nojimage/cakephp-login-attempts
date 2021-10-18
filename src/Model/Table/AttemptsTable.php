<?php
declare(strict_types=1);

namespace LoginAttempts\Model\Table;

use Cake\I18n\Time;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Attempts Model
 *
 * @method \LoginAttempts\Model\Entity\Attempt newEntity($data = null, array $options = [])
 * @method \LoginAttempts\Model\Entity\Attempt[] newEntities(array $data, array $options = [])
 * @method \LoginAttempts\Model\Entity\Attempt patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \LoginAttempts\Model\Entity\Attempt[] patchEntities($entities, array $data, array $options = [])
 * @method \LoginAttempts\Model\Entity\Attempt get($primaryKey, $options = [])
 * @method \LoginAttempts\Model\Entity\Attempt|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 */
class AttemptsTable extends Table implements AttemptsTableInterface
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->setTable('attempts');
        $this->setDisplayField('ip');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created_at' => 'new',
                ],
            ],
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', 'create');

        $validator
            ->requirePresence('ip', 'create')
            ->notEmptyString('ip')
            ->add('ip', 'ip', [
                'rule' => 'ip',
                'message' => __d('login_attemts', 'invalid IP address'),
            ]);

        $validator
            ->requirePresence('action', 'create')
            ->notEmptyString('action');

        $validator
            ->requirePresence('expires', 'create')
            ->notEmptyDateTime('expires');

        $validator
            ->requirePresence('created_at', 'create')
            ->notEmptyDateTime('created_at');

        return $validator;
    }

    /**
     * record on login failed
     *
     * @param string $ip A request client ip.
     * @param string $action A request target action.
     * @param string $duration Duration to disable login.
     * @return bool
     */
    public function fail($ip, $action, $duration)
    {
        $attempt = $this->newEntity([
            'ip' => $ip,
            'action' => $action,
            'expires' => Time::parse($duration),
            'created_at' => Time::now(),
        ]);

        return $this->save($attempt);
    }

    /**
     * check attempts less than $limit
     *
     * @param string $ip A request client ip.
     * @param string $action A request target action.
     * @param int $limit Number of trial limitation.
     * @return bool
     */
    public function check($ip, $action, $limit)
    {
        $count = $this->find()->where([
                'ip' => $ip,
                'action' => $action,
                'expires >=' => Time::now(),
            ])->count();

        return $count < $limit;
    }

    /**
     * reset on login success
     *
     * @param string $ip A request client ip.
     * @param string $action A request target action.
     * @return bool
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
     * @return bool
     */
    public function cleanup()
    {
        return $this->deleteAll([
                'expires <' => Time::now(),
        ]);
    }
}
