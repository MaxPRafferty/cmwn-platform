<?php

namespace Flip\Delegator;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Creates the delegator for the flip service
 */
class FlipServiceDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        return new FlipServiceDelegator($callback(), $container->get(EventManagerInterface::class));
    }
}
