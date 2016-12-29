<?php

namespace Security\Authentication;

use Security\ChangePasswordUser;
use Security\Exception\ChangePasswordException;
use Security\GuestUser;
use Security\SecurityUserInterface;
use Zend\Authentication\AuthenticationService as ZfAuthService;

/**
 * Authentication service that will always return a user type for getIdentity
 */
class AuthenticationService extends ZfAuthService
{
    /**
     * Returns the identity from storage or GuestUser if no identity is available
     *
     * If the user needs to change their password, than an exception is thrown
     *
     * @throws ChangePasswordException
     * @return SecurityUserInterface
     */
    public function getIdentity()
    {
        $identity = parent::getIdentity();
        if ($identity instanceof ChangePasswordUser) {
            throw new ChangePasswordException($identity);
        }

        return !$identity instanceof SecurityUserInterface
            ? new GuestUser()
            : $identity;
    }
}
