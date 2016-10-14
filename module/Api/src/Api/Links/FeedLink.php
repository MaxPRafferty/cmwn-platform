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
     * @param string $userId
     * @param null $feedId
     */
    public function __construct($userId, $feedId = null)
    {
        parent::__construct('feed');
        $this->setProps(['label' => 'Feed']);
        $this->setRoute('api.rest.feed', ['user_id' => $userId, 'feed_id' => $feedId]);
    }
}
