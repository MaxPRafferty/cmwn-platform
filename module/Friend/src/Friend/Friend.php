<?php

namespace Friend;

use User\User;

/**
 * Class Friend
 * @package Friend
 */
class Friend extends User implements FriendInterface
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
     * @param array $array
     */
    public function exchangeArray(array $array)
    {
        parent::exchangeArray($array);
        $this->setFriendStatus($array['friend_status']);
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
