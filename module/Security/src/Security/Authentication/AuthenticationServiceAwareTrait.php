<?php

namespace Security\Authentication;

use Zend\Authentication\AuthenticationServiceInterface;

/**
 * Trait AuthenticationServiceAwareTrait
 *
 * ${CARET}
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
    public function getAuthenticationService()
    {
        return $this->authService;
    }
}
