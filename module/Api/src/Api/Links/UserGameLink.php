<?php

namespace Api\Links;

use User\UserInterface;
use ZF\Hal\Link\Link;

/**
 * Hal link for user games
 */
class UserGameLink extends Link
{
    /**
     * UserGameLink constructor.
     * @param string | UserInterface $user
     */
    public function __construct($user)
    {
        parent::__construct('games');
        $userId = $user instanceof UserInterface? $user->getUserId() : $user;
        $this->setProps(['label' => 'Games']);
        $this->setRoute('api.rest.user-game', ['user_id' => $userId], ['reuse_matched_params' => false]);
    }
}
