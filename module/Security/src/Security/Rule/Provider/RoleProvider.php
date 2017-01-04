<?php

namespace Security\Rule\Provider;

use Rule\Provider\ProviderInterface;
use Zend\Authentication\AuthenticationServiceInterface;

/**
 * Provides the role set on the authenticated user
 */
class RoleProvider implements ProviderInterface
{
    const PROVIDER_NAME = 'active_role';

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * RoleProvider constructor.
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
        return static::PROVIDER_NAME;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->authService->getIdentity()->getRole();
    }
}
