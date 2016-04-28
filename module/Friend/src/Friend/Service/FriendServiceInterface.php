<?php

namespace Friend\Service;

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
}
