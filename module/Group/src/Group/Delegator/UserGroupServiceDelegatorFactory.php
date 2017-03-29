<?php

namespace Group\Delegator;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Creates a UserGroupServiceDelegator
 */
class UserGroupServiceDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        return new UserGroupServiceDelegator(
            $callback(),
            $container->get(EventManagerInterface::class)
        );
    }
}
