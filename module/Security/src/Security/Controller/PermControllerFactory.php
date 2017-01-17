<?php

namespace Security\Controller;

use Interop\Container\ContainerInterface;
use Security\Utils\PermissionTableFactory;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class PermControllerFactory
 */
class PermControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new PermController($container->get(PermissionTableFactory::class));
    }
}
