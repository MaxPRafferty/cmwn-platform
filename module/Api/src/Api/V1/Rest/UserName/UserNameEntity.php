<?php

namespace Api\V1\Rest\UserName;

use ZF\Hal\Entity;

/**
 * Class UserNameEntity
 *
 * Hal Link for the Username
 */
class UserNameEntity extends Entity
{
    /**
     * UserNameEntity constructor.
     *
     * @param array|object $userName
     */
    public function __construct($userName)
    {
        parent::__construct(new \ArrayObject(), null);

        $this->entity['user_name'] = $userName;
    }
}
