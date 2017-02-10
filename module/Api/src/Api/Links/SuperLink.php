<?php

namespace Api\Links;

use User\UserInterface;
use ZF\Hal\Link\Link;

/**
 * Added to a user entity when that entity can be promoted to a super user
 */
class SuperLink extends Link
{
    /**
     * SuperLink constructor.
     * @param UserInterface | null | string $user
     */
    public function __construct($user = null)
    {
        parent::__construct('super');

        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $this->setProps(['label' => 'Super']);
        $this->setRoute('api.rest.super', ['user_id' => $userId]);
    }
}
