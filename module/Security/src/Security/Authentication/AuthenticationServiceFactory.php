<?php

namespace Security\Authentication;

use Interop\Container\ContainerInterface;
use Zend\Authentication\Storage\Session;
use Zend\EventManager\EventManagerInterface;
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
        return new AuthenticationService(
            $container->get(EventManagerInterface::class),
            new Session(null, null, $container->get(SessionManager::class)),
            $container->get(AuthAdapter::class)
        );
    }
}
