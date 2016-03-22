<?php

namespace Security\Authentication;

use Zend\Authentication\AuthenticationServiceInterface;

/**
 * Interface AuthenticationServiceAwareInterface
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
    public function getAuthenticationService();
}
