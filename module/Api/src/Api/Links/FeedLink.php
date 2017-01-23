<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class FeedLink
 * @package Api\Links
 */
class FeedLink extends Link
{
    /**
     * FeedLink constructor.
     * @param null $feedId
     */
    public function __construct($feedId = null)
    {
        parent::__construct('feed');
        $this->setProps(['label' => 'Feed']);
        $this->setRoute('api.rest.feed', ['feed_id' => $feedId]);
    }
}
