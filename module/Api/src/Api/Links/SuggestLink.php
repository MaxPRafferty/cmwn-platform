<?php

namespace Api\Links;

use User\UserInterface;
use ZF\Hal\Link\Link;

/**
 * Class SuggestLink
 */
class SuggestLink extends Link
{
    /**
     * FriendLink constructor.
     * @param string $user
     */
    public function __construct($user)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        parent::__construct('suggested_friends');
        $this->setProps(['label' => 'Suggested Friends']);
        $this->setRoute('api.rest.suggest', ['user_id' => $userId], ['reuse_matched_params' => false]);
    }
}
