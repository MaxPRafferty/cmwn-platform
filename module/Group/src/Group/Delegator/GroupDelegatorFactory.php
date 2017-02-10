<?php

namespace Group\Delegator;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Creates the group service delegator
 */
class GroupDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        return new GroupDelegator(
            $callback(),
            $container->get(EventManagerInterface::class)
        );
    }
}
