<?php

namespace Api\Links;

use User\UserInterface;
use ZF\Hal\Link\Link;

/**
 * Class ProfileLink
 *
 * Hal Link for the Profile
 */
class ProfileLink extends Link
{
    /**
     * ProfileLink constructor.
     *
     * @param string $userId
     */
    public function __construct($userId)
    {
        $userId = $userId instanceof UserInterface ? $userId->getUserId() : $userId;
        $userName = $userId instanceof UserInterface ? $userId->getUserName() : null;
        parent::__construct('profile');
        $this->setProps(['label' => trim($userName . ' Profile')]);
        $this->setRoute('api.rest.user', ['user_id' => $userId]);
    }
}
