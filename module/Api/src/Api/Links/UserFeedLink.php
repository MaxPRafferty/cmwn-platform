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
     * @param $userId
     * @param string | null $feedId
     */
    public function __construct($userId, string $feedId = null)
    {
        parent::__construct('user_feed');
        $this->setProps(['label' => 'User Feed']);
        $this->setRoute('api.rest.feed-user', ['user_id' => $userId, 'feed_id' => $feedId]);
    }
}
