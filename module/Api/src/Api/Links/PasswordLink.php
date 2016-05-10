<?php

namespace Api\Links;

use User\UserInterface;
use ZF\Hal\Link\Link;

/**
 * Class PasswordLink
 */
class PasswordLink extends Link
{
    /**
     * PasswordLink constructor.
     * @param string $userId
     */
    public function __construct($userId)
    {
        $userId = $userId instanceof UserInterface ? $userId->getUserId() : $userId;
        parent::__construct('password');
        $this->setProps(['label' => 'Change Password']);
        $this->setRoute('api.rest.password', ['user_id' => $userId]);
    }
}
