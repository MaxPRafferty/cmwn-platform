<?php

namespace Api\Links;

use User\UserInterface;
use ZF\Hal\Link\Link;

/**
 * Class ResetLink
 */
class ResetLink extends Link
{
    /**
     * ResetLink constructor.
     *
     * @param string $user
     */
    public function __construct($user)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        parent::__construct('reset');
        $this->setProps(['label' => 'Reset Password']);
        $this->setRoute('api.rest.reset', ['user_id' => $userId], ['reuse_matched_params' => false]);
    }
}
