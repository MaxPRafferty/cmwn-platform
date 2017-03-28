<?php

namespace Feed;

/**
 * Interface to make an entity feedable
 */
interface FeedableInterface
{
    /**
     * @return string
     */
    public function getFeedMessage(): string;

    /**
     * @return array
     */
    public function getFeedMeta(): array;

    /**
     * @return int
     */
    public function getFeedVisiblity(): int;

    /**
     * @return string
     */
    public function getFeedType(): string;

    /**
     * @return string
     */
    public function getFeedTitle(): string;

    /**
     * @return string
     */
    public function getFeedPriority(): string;

    /**
     * @return string
     */
    public function getFeedSender(): string;

    /**
     * @return string
     */
    public function getFeedTypeVersion(): string;
}
