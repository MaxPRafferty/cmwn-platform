<?php

namespace Address\Delegator;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Class AddressDelegatorFactory
 * @package Address\Delegator
 */
class GroupAddressDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        return new GroupAddressDelegator($callback(), $container->get(EventManagerInterface::class));
    }
}
