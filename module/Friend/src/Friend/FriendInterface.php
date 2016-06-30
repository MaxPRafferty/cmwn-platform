<?php

namespace Friend;

/**
 * Interface FriendInterface
 */
interface FriendInterface
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
    public function canFriend();

    /**
     * Whether the active user is friends with this user
     *
     * @return bool
     */
    public function isFriend();

    /**
     * Whether the active user requested to be friends with this user
     *
     * @return bool
     */
    public function isRequested();

    /**
     * Whether this user as requested the active user to be friends
     *
     * @return bool
     */
    public function isPending();

    /**
     * Gets the string status of a friend
     *
     * @return string
     */
    public function getFriendStatus();

    /**
     * Sets the string status of a friend
     *
     * @param string $status
     * @return string
     */
    public function setFriendStatus($status);
}
