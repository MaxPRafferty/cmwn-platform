<?php

namespace Security\Controller;

use Security\Authorization\Rbac;
use Security\Utils\PermissionTableFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class PermControllerFactory
 */
class PermControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator instanceof ServiceLocatorAwareInterface
            ? $serviceLocator->getServiceLocator()
            : $serviceLocator;

        /** @var Rbac $rbac */
        $rbac = $serviceLocator->get(PermissionTableFactory::class);

        return new PermController($rbac);
    }
}
