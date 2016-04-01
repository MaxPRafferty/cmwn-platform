<?php

namespace Api\V1\Rest\UserName;

use Zend\Stdlib\ArraySerializableInterface;
use ZF\Hal\Entity;

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
