<?php

namespace Org\Delegator;

use Interop\Container\ContainerInterface;
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
        return new OrganizationServiceDelegator($callback());
    }
}
