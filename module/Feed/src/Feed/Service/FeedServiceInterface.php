<?php

namespace Feed\Service;

use Feed\FeedInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Interface FeedServiceInterface
 * @package Feed\Service
 */
interface FeedServiceInterface
{
    /**
     * @param FeedInterface $feed
     * @return bool
     */
    public function createFeed(FeedInterface $feed);

    /**
     * @param string $feedId
     * @param null $where
     * @param null $prototype
     * @return mixed | FeedInterface
     */
    public function fetchFeed($feedId, $where = null, $prototype = null);

    /**
     * @param null $where
     * @param null $prototype
     * @return mixed | DbSelect
     */
    public function fetchAll($where = null, $prototype = null);

    /**
     * @param FeedInterface $feed
     * @return bool
     */
    public function updateFeed(FeedInterface $feed);

    /**
     * @param FeedInterface $feed
     * @param bool $soft
     * @return bool
     */
    public function deleteFeed(FeedInterface $feed, $soft = true);
}
