<?php

namespace Friend;

use Feed\FeedableInterface;
use \User\UserInterface;

/**
 * Interface FriendInterface
 */
interface FriendInterface extends UserInterface, FeedableInterface
{
    const CAN_FRIEND  = 'CAN_FRIEND';
    const FRIEND      = 'FRIEND';
    const REQUESTED   = 'NEEDS_YOUR_ACCEPTANCE';
    const PENDING     = 'PENDING';
    const CANT_FRIEND = 'CANT_FRIEND';
    const NOT_FRIENDS = 'NOT_FRIENDS';

    /**
     * Whether the active user can friend this user
     *
     * @return bool
     */
    public function canFriend() : bool;

    /**
     * Whether the active user is friends with this user
     *
     * @return bool
     */
    public function isFriend() : bool;

    /**
     * Whether the active user requested to be friends with this user
     *
     * @return bool
     */
    public function isRequested() : bool;

    /**
     * Whether this user as requested the active user to be friends
     *
     * @return bool
     */
    public function isPending() : bool;

    /**
     * Gets the string status of a friend
     *
     * @return string
     */
    public function getFriendStatus() : string;

    /**
     * Sets the string status of a friend
     *
     * @param string $status
     * @return FriendInterface
     */
    public function setFriendStatus(string $status = null) : FriendInterface;
}
