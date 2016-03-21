<?php

namespace Security\Authorization;

use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RouterListenerFactory
 */
class RouteListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var AuthenticationServiceInterface $authService */
        $authService = $serviceLocator->get('Security\Authentication\AuthenticationService');

        /** @var \Security\Service\SecurityOrgService $orgService */
        $orgService  = $serviceLocator->get('Security\Service\SecurityOrgService');

        /** @var \Security\Authorization\Rbac $rbac */
        $rbac        = $serviceLocator->get('Security\Authorization\Rbac');

        $config = $serviceLocator->get('config');
        $config = isset($config['cmwn-security']) ? $config['cmwn-security'] : [];
        return new RouteListener($config, $authService, $orgService, $rbac);
    }
}
