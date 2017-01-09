<?php

namespace Org\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class OrganizationServiceFactory
 */
class OrganizationServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new OrganizationService($container->get('OrganizationsTable'));
    }
}
