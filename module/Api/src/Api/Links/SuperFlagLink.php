<?php

namespace Api\Links;

use User\UserInterface;
use ZF\Hal\Link\Link;

/**
 * Class GroupResetLink
 * @package Api\Links
 */
class SuperFlagLink extends Link
{
    /**
     * SuperFlagLink constructor.
     * @param string | UserInterface $user
     */
    public function __construct($user = null)
    {
        parent::__construct('super_flag');

        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;

        $this->setProps(['label' => 'set super for users']);
        $this->setRoute('api.rest.super-flag', ['user_id' => $userId]);
    }
}
