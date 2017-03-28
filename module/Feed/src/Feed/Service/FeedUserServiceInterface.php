<?php

namespace Feed\Service;

use Application\Exception\DuplicateEntryException;
use Application\Exception\NotFoundException;
use Feed\UserFeedInterface;
use User\UserInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Describes actions that can be performed on user feed
 */
interface FeedUserServiceInterface
{
    /**
     * @param string | UserInterface $user
     * @param UserFeedInterface $feed
     * @throws DuplicateEntryException
     * @return bool
     */
    public function attachFeedForUser($user, UserFeedInterface $feed) : bool;

    /**
     * @param string | UserInterface $user
     * @param string $feedId
     * @param $where
     * @param UserFeedInterface $prototype
     * @throws NotFoundException
     * @return UserFeedInterface
     */
    public function fetchFeedForUser(
        $user,
        string $feedId,
        $where = null,
        UserFeedInterface $prototype = null
    ) : UserFeedInterface;

    /**
     * @param $user
     * @param $where
     * @param $prototype
     * @return AdapterInterface
     */
    public function fetchAllFeedForUser($user, $where = null, UserFeedInterface $prototype = null) : AdapterInterface;

    /**
     * @param string | UserInterface $user
     * @param UserFeedInterface $feed
     * @return bool
     */
    public function updateFeedForUser($user, UserFeedInterface $feed) : bool;

    /**
     * @param string | UserInterface $user
     * @param UserFeedInterface $feed
     * @return bool
     */
    public function deleteFeedForUser($user, UserFeedInterface $feed) : bool;
}
