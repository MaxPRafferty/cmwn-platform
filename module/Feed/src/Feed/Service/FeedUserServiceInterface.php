<?php

namespace Feed\Service;

use Application\Exception\DuplicateEntryException;
use Application\Exception\NotFoundException;
use Feed\UserFeedInterface;
use User\UserInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Interface FeedUserServiceInterface
 * @package Feed\Service
 */
interface FeedUserServiceInterface
{
    /**
     * @param string | UserInterface $user
     * @param UserFeedInterface $feed
     * @throws DuplicateEntryException
     * @return bool
     */
    public function attachFeedForUser($user, UserFeedInterface $feed);

    /**
     * @param string | UserInterface $user
     * @param string $feedId
     * @param $where
     * @param UserFeedInterface $prototype
     * @throws NotFoundException
     * @return UserFeedInterface
     */
    public function fetchFeedForUser($user, string $feedId, $where = null, UserFeedInterface $prototype = null);

    /**
     * @param $user
     * @param $where
     * @param $prototype
     * @return DbSelect
     */
    public function fetchAllFeedForUser($user, $where = null, UserFeedInterface $prototype = null);

    /**
     * @param string | UserInterface $user
     * @param UserFeedInterface $feed
     * @return bool
     */
    public function updateFeedForUser($user, UserFeedInterface $feed);

    /**
     * @param string | UserInterface $user
     * @param UserFeedInterface $feed
     * @return bool
     */
    public function deleteFeedForUser($user, UserFeedInterface $feed);
}
