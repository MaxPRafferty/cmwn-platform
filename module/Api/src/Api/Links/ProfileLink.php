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
        parent::__construct('profile');
        $this->setRoute('api.rest.user', ['user_id' => $userId]);
    }
}
