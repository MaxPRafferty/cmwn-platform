<?php

namespace Friend;

use User\User;
use User\UserInterface;

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
     * @inheritdoc
     */
    public function getArrayCopy(): array
    {
        $array = parent::getArrayCopy();
        $array['friend_id']     = $this->getUserId();
        $array['friend_status'] = $this->getFriendStatus();
        unset($array['user_id']);
        return $array;
    }

    /**
     * @inheritdoc
     */
    public function exchangeArray(array $array): UserInterface
    {
        parent::exchangeArray($array);
        $this->setFriendStatus($array['friend_status']);
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return $this->type;
    }
}
