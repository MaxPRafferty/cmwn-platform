<?php

namespace Security\Factory;

use Interop\Container\ContainerInterface;
use Security\Listeners\ExpireAuthSessionListener;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\Container;
use Zend\Session\SessionManager;

/**
 * Class ExpireAuthSessionListenerFactory
 */
class ExpireAuthSessionListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ExpireAuthSessionListener(new Container('auth_timer', $container->get(SessionManager::class)));
    }
}
