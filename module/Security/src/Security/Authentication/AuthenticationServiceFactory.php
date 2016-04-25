<?php

namespace Security\Authentication;

use Zend\Authentication\Storage\Session;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\SessionManager;

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
        /** @var SessionManager $session */
        $session = $serviceLocator->get(SessionManager::class);
        return new AuthenticationService(new Session(null, null, $session));
    }
}
