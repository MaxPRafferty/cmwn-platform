<?php

namespace Feed;

/**
 * Trait to implement defaults from feedable interface
 */
trait FeedableTrait
{
    /**
     * @inheritdoc
     */
    public function getFeedSender(): string
    {
        return (string)null;
    }

    /**
     * @inheritdoc
     */
    public function getFeedTypeVersion(): string
    {
        return FeedInterface::FEED_TYPE_VERSION;
    }
}
