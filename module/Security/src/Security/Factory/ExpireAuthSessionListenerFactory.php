<?php

namespace Security\Factory;

use Security\Listeners\ExpireAuthSessionListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

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
        /** @var \Zend\Session\SessionManager $session */
        $session = $serviceLocator->get('Zend\Session\SessionManager');
        return new ExpireAuthSessionListener(new Container('auth_timer', $session));
    }
}
