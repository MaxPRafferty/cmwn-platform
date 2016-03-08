<?php

namespace Security\Authentication;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CmwnAuthenticationServiceFactory
 * @package Security\Authentication
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new AuthenticationService(new Session());
    }
}
