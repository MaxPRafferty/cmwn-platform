<?php

namespace Skribble\Delegator;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Class SkribbleServiceDelegatorFactory
 */
class SkribbleServiceDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        return new SkribbleServiceDelegator($callback(), $container->get(EventManagerInterface::class));
    }
}
