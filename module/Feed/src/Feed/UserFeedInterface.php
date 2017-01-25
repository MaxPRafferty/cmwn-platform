<?php

namespace Feed;

/**
 * Interface UserFeedInterface
 * @package Feed
 */
interface UserFeedInterface extends FeedInterface
{
    /**
     * @return int
     */
    public function getReadFlag();

    /**
     * @param int $readFlag
     */
    public function setReadFlag($readFlag);
}
