<?php

namespace Org\Delegator;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Class OrganizationDelegatorFactory
 */
class OrganizationDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        return new OrganizationServiceDelegator($callback(), $container->get(EventManagerInterface::class));
    }
}
