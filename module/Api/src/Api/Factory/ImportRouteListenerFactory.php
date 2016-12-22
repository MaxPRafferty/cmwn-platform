<?php

namespace Api\Factory;

use Api\Listeners\ImportRouteListener;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Security\Service\SecurityOrgServiceInterface;

/**
 * Class ImportRouteListenerFactory
 */
class ImportRouteListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ImportRouteListener($container->get(SecurityOrgServiceInterface::class));
    }
}
