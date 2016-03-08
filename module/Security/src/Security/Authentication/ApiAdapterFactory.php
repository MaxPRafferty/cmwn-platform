<?php

namespace Security\Authentication;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CmwnAuthenticationAdapterFactory
 * @package Security\Authentication
 * @codeCoverageIgnore
 */
class ApiAdapterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Zend\Authentication\AuthenticationService $securityService */
        $securityService = $serviceLocator->get('Security\Authentication\AuthenticationService');
        return new ApiAdapter($securityService);
    }
}
