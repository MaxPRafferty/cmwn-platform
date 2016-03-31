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
        /** @var \Zend\Session\SessionManager $session */
        $session = $serviceLocator->get('Zend\Session\SessionManager');
        return new AuthenticationService(new Session(null, null, $session));
    }
}
