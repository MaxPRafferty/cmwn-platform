<?php

namespace Api\V1\Rest\User;

use User\User;
use User\UserInterface;

/**
 * Class UserEntity
 *
 * @package Api\V1\Rest\User
 */
class UserEntity extends User implements UserInterface
{
    protected $type;

    /**
     * @param $type
     */
    protected function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
