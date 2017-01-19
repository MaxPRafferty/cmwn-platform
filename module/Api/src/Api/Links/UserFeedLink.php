<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class UserFeedLink
 * @package Api\Links
 */
class UserFeedLink extends Link
{
    /**
     * UserFeedLink constructor.
     * @param string $userId
     * @param string | null $feedId
     */
    public function __construct(string $userId, string $feedId = null)
    {
        parent::__construct('feed');
        $this->setProps(['label' => 'Feed']);
        $this->setRoute('api.rest.feed', ['user_id' => $userId, 'feed_id' => $feedId]);
    }
}
