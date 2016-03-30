<?php

namespace Api\V1\Rest\User;

use Api\Links\PasswordLink;
use User\UserInterface;
use ZF\Hal\Entity;

/**
 * Class ResetEntity
 * @package Api\V1\Rest\User
 */
class ResetEntity extends Entity
{
    public function __construct(UserInterface $user)
    {
        parent::__construct(['message' => 'Change Password'], $user->getUserId());
        $this->getLinks()->add(new PasswordLink($user));
    }
}
