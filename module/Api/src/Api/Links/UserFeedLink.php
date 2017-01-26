<?php

namespace Api\Links;

use User\UserInterface;
use ZF\Hal\Link\Link;

/**
 * Class UserFeedLink
 * @package Api\Links
 */
class UserFeedLink extends Link
{
    /**
     * UserFeedLink constructor.
     * @param string | UserInterface $user
     * @param string | null $feedId
     */
    public function __construct($user, string $feedId = null)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        parent::__construct('user_feed');
        $this->setProps(['label' => 'User Feed']);
        $this->setRoute('api.rest.feed-user', ['user_id' => $userId, 'feed_id' => $feedId]);
    }
}
