<?php

namespace Feed\Service;

use Feed\FeedInterface;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Describes actions that can be performed on user feed
 */
interface FeedServiceInterface
{
    /**
     * @param FeedInterface $feed
     * @return bool
     */
    public function createFeed(FeedInterface $feed) : bool;

    /**
     * @param string $feedId
     * @param null $where
     * @param FeedInterface | null $prototype
     * @return mixed | FeedInterface
     */
    public function fetchFeed(string $feedId, $where = null, FeedInterface $prototype = null) : FeedInterface;

    /**
     * @param null $where
     * @param FeedInterface | null $prototype
     * @return mixed | DbSelect
     */
    public function fetchAll($where = null, FeedInterface $prototype = null) : AdapterInterface;

    /**
     * @param FeedInterface $feed
     * @return bool
     */
    public function updateFeed(FeedInterface $feed) : bool;

    /**
     * @param FeedInterface $feed
     * @param bool $soft
     * @return bool
     */
    public function deleteFeed(FeedInterface $feed, $soft = true) : bool;
}
