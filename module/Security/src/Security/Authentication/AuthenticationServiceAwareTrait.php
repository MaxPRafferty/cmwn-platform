<?php

namespace Security\Authentication;

use Zend\Authentication\AuthenticationServiceInterface;

/**
 * A trait that helps satisfied AuthenticationServiceAwareInterface
 */
trait AuthenticationServiceAwareTrait
{
    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * @param AuthenticationServiceInterface $authService
     */
    public function setAuthenticationService(AuthenticationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @return AuthenticationServiceInterface
     */
    public function getAuthenticationService(): AuthenticationServiceInterface
    {
        return $this->authService;
    }
}
