<?php

namespace Security\Authentication;

use Security\Service\SecurityServiceInterface;
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
        /** @var SecurityServiceInterface $securityService */
        $securityService = $serviceLocator->get(SecurityServiceInterface::class);
        return new AuthAdapter($securityService);
    }
}
