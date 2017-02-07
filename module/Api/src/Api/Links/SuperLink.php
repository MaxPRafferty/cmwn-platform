<?php

namespace Api\Links;

use User\UserInterface;
use ZF\Hal\Link\Link;

/**
 * Class SuperLink
 * @package Api\Links
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
