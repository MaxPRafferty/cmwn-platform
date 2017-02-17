<?php

namespace Api\Links;

use User\UserInterface;
use ZF\Hal\Link\Link;

/**
 * Class FriendLink
 */
class FriendLink extends Link
{
    /**
     * FriendLink constructor.
     *
     * @param string | UserInterface $user
     * @param null $friendId
     */
    public function __construct($user, $friendId = null)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        parent::__construct('friend');
        $this->setProps(['label' => 'My Friends']);
        $this->setRoute(
            'api.rest.friend',
            ['user_id' => $userId, 'friend_id' => $friendId],
            ['reuse_matched_params' => false]
        );
    }
}
