<?php

namespace Api\Factory;

use Api\Listeners\ImportRouteListener;
use Interop\Container\ContainerInterface;
use Security\Service\SecurityOrgService;
use Zend\ServiceManager\Factory\FactoryInterface;

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
        return new ImportRouteListener($container->get(SecurityOrgService::class));
    }
}
