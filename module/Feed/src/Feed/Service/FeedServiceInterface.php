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
     * @param FeedInterface | null $prototype
     * @return mixed | FeedInterface
     */
    public function fetchFeed(string $feedId, $where = null, FeedInterface $prototype = null);

    /**
     * @param null $where
     * @param FeedInterface | null $prototype
     * @return mixed | DbSelect
     */
    public function fetchAll($where = null, FeedInterface $prototype = null);

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
