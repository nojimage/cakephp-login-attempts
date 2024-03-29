<?php
declare(strict_types=1);

namespace LoginAttempts\Model\Table;

use Cake\I18n\DateTime;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use LoginAttempts\Model\Entity\Attempt;
use function __d;

/**
 * Attempts Model
 *
 * @method \LoginAttempts\Model\Entity\Attempt newEntity($data = null, array $options = [])
 * @method \LoginAttempts\Model\Entity\Attempt[] newEntities(array $data, array $options = [])
 * @method \LoginAttempts\Model\Entity\Attempt patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \LoginAttempts\Model\Entity\Attempt[] patchEntities($entities, array $data, array $options = [])
 * @method \LoginAttempts\Model\Entity\Attempt get($primaryKey, $options = [])
 * @method \LoginAttempts\Model\Entity\Attempt|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
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
                'message' => __d('login_attempts', 'invalid IP address'),
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
     * @return \LoginAttempts\Model\Entity\Attempt|false
     */
    public function fail(string $ip, string $action, string $duration): Attempt|false
    {
        $attempt = $this->newEntity([
            'ip' => $ip,
            'action' => $action,
            'expires' => DateTime::parse($duration),
            'created_at' => DateTime::now(),
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
    public function check(string $ip, string $action, int $limit): bool
    {
        $count = $this->find()->where([
                'ip' => $ip,
                'action' => $action,
                'expires >=' => DateTime::now(),
            ])->count();

        return $count < $limit;
    }

    /**
     * reset on login success
     *
     * @param string $ip A request client ip.
     * @param string $action A request target action.
     * @return int
     */
    public function reset(string $ip, string $action): int
    {
        return $this->deleteAll([
                'ip' => $ip,
                'action' => $action,
        ]);
    }

    /**
     * cleanup expired data
     *
     * @return int
     */
    public function cleanup(): int
    {
        return $this->deleteAll([
                'expires <' => DateTime::now(),
        ]);
    }
}
