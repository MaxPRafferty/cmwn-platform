<?php

namespace Friend\Service;

use Friend\NotFriendsException;
use User\UserInterface;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Interface FriendServiceInterface
 */
interface FriendServiceInterface
{

    /**
     * Fetches all the friends for a user
     *
     * @param string|UserInterface $user
     * @param null|array|PredicateInterface $where
     * @param null|UserInterface|object $prototype
     * @return DbSelect
     */
    public function fetchFriendsForUser($user, $where = null, $prototype = null);

    /**
     * Adds a friend to a user
     *
     * @param string|UserInterface $user
     * @param string|UserInterface $friend
     * @return bool
     */
    public function attachFriendToUser($user, $friend);

    /**
     * Removes a friend from a user
     *
     * @param string|UserInterface $user
     * @param string|UserInterface $friend
     * @return bool
     */
    public function detachFriendFromUser($user, $friend);

    /**
     * Fetches a friend for a user
     *
     * SELECT *
     * FROM user_friends AS uf
     *    LEFT JOIN users AS u ON u.user_id = :friend_id
     * WHERE (uf.user_id = :user_id OR uf.friend_id = :user_id)
     *   AND (uf.user_id = :friend_id OR uf.friend_id = :friend_id)
     *
     * @param UserInterface|string $user
     * @param UserInterface|string $friend
     * @param null|object $prototype
     * @param null|string status
     * @throws NotFriendsException
     * @return object|UserInterface
     */
    public function fetchFriendForUser($user, $friend, $prototype = null);

    /**
     * Fetches the current friend status of a user
     *
     * @param UserInterface $user
     * @param UserInterface $friend
     * @return string
     */
    public function fetchFriendStatusForUser(UserInterface $user, UserInterface $friend);
}
