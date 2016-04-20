<?php

namespace Security\Authentication;

use Security\ChangePasswordUser;
use Security\Exception\ChangePasswordException;
use Security\GuestUser;
use User\UserInterface;
use Zend\Authentication\AuthenticationService as ZfAuthService;

/**
 * Class AuthenticationService
 */
class AuthenticationService extends ZfAuthService
{
    /**
     * Returns the identity from storage or null if no identity is available
     *
     * @throws ChangePasswordException
     * @return UserInterface|GuestUser
     */
    public function getIdentity()
    {
        $identity = parent::getIdentity();
        if ($identity instanceof ChangePasswordUser) {
            throw new ChangePasswordException($identity);
        }

        return $identity;
    }
}
