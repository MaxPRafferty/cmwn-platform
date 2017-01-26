<?php

namespace Suggest\Delegator;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Class SuggestedFriendServiceDelegatorFactory
 */
class SuggestedServiceDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        return new SuggestedServiceDelegator($callback(), $container->get(EventManagerInterface::class));
    }
}
