<?php

namespace Security\Authentication;

use Zend\Authentication\AuthenticationServiceInterface;

/**
 * A Class that is aware of the authentication service
 */
interface AuthenticationServiceAwareInterface
{
    /**
     * @param AuthenticationServiceInterface $authService
     */
    public function setAuthenticationService(AuthenticationServiceInterface $authService);

    /**
     * @return AuthenticationServiceInterface
     */
    public function getAuthenticationService(): AuthenticationServiceInterface;
}
