<?php

namespace Security\Factory;

use Security\Listeners\ExpireAuthSessionListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;
use Zend\Session\SessionManager;

/**
 * Class ExpireAuthSessionListenerFactory
 */
class ExpireAuthSessionListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var SessionManager $session */
        $session = $serviceLocator->get(SessionManager::class);
        return new ExpireAuthSessionListener(new Container('auth_timer', $session));
    }
}
