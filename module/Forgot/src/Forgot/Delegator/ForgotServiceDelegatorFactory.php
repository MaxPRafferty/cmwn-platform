<?php

namespace Forgot\Delegator;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Class ForgotServiceDelegatorFactory
 */
class ForgotServiceDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        return new ForgotServiceDelegator($callback(), $container->get(EventManagerInterface::class));
    }
}
