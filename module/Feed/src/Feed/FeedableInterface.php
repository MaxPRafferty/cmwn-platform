<?php

namespace Feed;

/**
 * Interface to make an entity feedable
 */
interface FeedableInterface
{
    public function getFeedMessage(): string;

    public function getFeedMeta(): array;

    public function getFeedVisiblity(): int;

    public function getFeedType(): string;

    public function getFeedTitle(): string;

    public function getFeedPriority(): string;
}
