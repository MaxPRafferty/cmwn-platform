<?php

namespace Security\Authentication;

use Interop\Container\ContainerInterface;
use Zend\Authentication\Storage\Session;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;

/**
 * Class AuthenticationServiceFactory
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AuthenticationService(new Session(null, null, $container->get(SessionManager::class)));
    }
}
