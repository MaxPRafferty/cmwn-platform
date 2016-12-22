<?php

namespace Security\Service;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class SecurityOrgServiceFactory
 */
class SecurityOrgServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SecurityOrgService($container->get(Adapter::class));
    }
}
