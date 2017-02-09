<?php

namespace Api\Links;

use User\UserInterface;
use ZF\Hal\Link\Link;

/**
 * Class UserFlipLink
 */
class UserFlipLink extends Link
{
    /**
     * UserFlipLink constructor.
     *
     * @param null | string | UserInterface $user
     */
    public function __construct($user = null)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        parent::__construct('user_flip');
        $this->setProps(['label' => 'My Earned Flips']);
        $this->setRoute('api.rest.flip-user', ['user_id' => $userId]);
    }
}
