<?php

namespace Security\Utils;

use Security\Authorization\Rbac;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class PermissionTableBuilderFactory
 */
class PermissionTableBuilderFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $rbac = $serviceLocator->get(Rbac::class);
        return new PermissionTableFactory($rbac);
    }
}
