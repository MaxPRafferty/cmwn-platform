<?php

namespace Api\V1\Rest\Friend;

use Friend\FriendInterface;
use Friend\FriendTrait;
use User\User;
use User\UserInterface;

/**
 * Class FriendEntity
 */
class FriendEntity extends User implements UserInterface, FriendInterface
{
    use FriendTrait;
    
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
        $array['friend_id']     = $this->getUserId();
        $array['friend_status'] = $this->getFriendStatus();
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
