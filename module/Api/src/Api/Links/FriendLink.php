<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class FriendLink
 */
class FriendLink extends Link
{
    /**
     * FriendLink constructor.
     * @param string $userId
     */
    public function __construct($userId, $friendId)
    {
        parent::__construct('friend');
        $this->setProps(['label' => 'My Friends']);
        $this->setRoute(
            'api.rest.friend',
            ['user_id' => $userId, 'friend_id' => $friendId],
            ['reuse_matched_params' => false]
        );
    }
}
