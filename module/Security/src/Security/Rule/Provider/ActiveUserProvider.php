<?php

namespace Security\Rule\Provider;

use Rule\Provider\ProviderInterface;
use Zend\Authentication\AuthenticationServiceInterface;

/**
 * Provides the current logged in user
 *
 * If there is no logged in user, a guest user is provided
 */
class ActiveUserProvider implements ProviderInterface
{
    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * ActiveUserProvider constructor.
     *
     * @param AuthenticationServiceInterface $service
     */
    public function __construct(AuthenticationServiceInterface $service)
    {
        $this->authService = $service;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'active_user';
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->authService->getIdentity();
    }
}
