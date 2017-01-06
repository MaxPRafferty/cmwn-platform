<?php

namespace Security\Utils;

use Interop\Container\ContainerInterface;
use Security\Authorization\Rbac;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class PermissionTableBuilderFactory
 * @todo move to dev module
 */
class PermissionTableBuilderFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new PermissionTableFactory($container->get(Rbac::class));
    }
}
