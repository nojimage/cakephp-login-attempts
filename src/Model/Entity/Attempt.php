<?php

namespace LoginAttempts\Model\Entity;

use Cake\ORM\Entity;
use DateTimeInterface;

/**
 * Attempt Entity.
 *
 * @property int $id
 * @property string $ip
 * @property string $action
 * @property DateTimeInterface $expires
 * @property DateTimeInterface $created_at
 */
class Attempt extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'ip' => true,
        'action' => true,
        'expires' => true,
        'created_at' => true,
    ];
}
