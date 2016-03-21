<?php

namespace Api\Factory;

use Api\Listeners\ScopeListener;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ScopeListenerFactory
 * @codeCoverageIgnore
 */
class ScopeListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Security\Authorization\Rbac $rbac */
        $rbac        = $serviceLocator->get('Security\Authorization\Rbac');

        /** @var AuthenticationServiceInterface $authService */
        $authService = $serviceLocator->get('Security\Authentication\AuthenticationService');

        return new ScopeListener($rbac, $authService);
    }
}
