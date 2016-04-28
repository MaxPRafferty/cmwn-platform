<?php

namespace Api\V1\Rest\Friend;

use Api\V1\Rest\User\UserEntity;
use User\User;
use User\UserInterface;

/**
 * Class FriendEntity
 */
class FriendEntity extends User implements UserInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * Replaces the user_id with friend_id
     *
     * @return string[]
     */
    public function getArrayCopy()
    {
        $array = parent::getArrayCopy();
        $array['friend_id'] = $this->getUserId();
        unset($array['user_id']);
        return $array;
    }

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
