<?php

namespace Security\Authentication;

use Security\Service\SecurityService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AuthAdapterFactory
 * @package Security\Authentication
 */
class AuthAdapterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var SecurityService $securityService */
        $securityService = $serviceLocator->get('Security\Service\SecurityService');
        return new AuthAdapter($securityService);
    }
}
