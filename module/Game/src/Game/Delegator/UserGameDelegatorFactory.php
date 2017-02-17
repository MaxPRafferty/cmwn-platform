<?php

namespace Game\Delegator;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Class UserGameDelegatorFactory
 * @package Game\Delegator
 */
class UserGameDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        return new UserGameServiceDelegator($callback(), $container->get(EventManagerInterface::class));
    }
}
