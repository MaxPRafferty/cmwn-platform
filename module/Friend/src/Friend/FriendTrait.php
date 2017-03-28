<?php

namespace Friend;

/**
 * Trait FriendTrait
 */
trait FriendTrait
{
    /**
     * @var string
     */
    protected $friendStatus = FriendInterface::NOT_FRIENDS;

    /**
     * Whether the active user can friend this user
     *
     * @return bool
     */
    public function canFriend() : bool
    {
        return $this->friendStatus === FriendInterface::CAN_FRIEND;
    }

    /**
     * Whether the active user is friends with this user
     *
     * @return bool
     */
    public function isFriend() : bool
    {
        return $this->friendStatus === FriendInterface::FRIEND;
    }

    /**
     * Whether the active user requested to be friends with this user
     *
     * @return bool
     */
    public function isRequested() : bool
    {
        return $this->friendStatus === FriendInterface::REQUESTED;
    }

    /**
     * Whether this user as requested the active user to be friends
     *
     * @return bool
     */
    public function isPending() : bool
    {
        return $this->friendStatus === FriendInterface::PENDING;
    }

    /**
     * Gets the string status of a friend
     *
     * @return string
     */
    public function getFriendStatus() : string
    {
        return (string)$this->friendStatus;
    }

    /**
     * Sets the string status of a friend
     *
     * @param string $status
     * @return FriendInterface
     */
    public function setFriendStatus(string $status = null) : FriendInterface
    {
        $this->friendStatus = $status;
        return $this;
    }
}
