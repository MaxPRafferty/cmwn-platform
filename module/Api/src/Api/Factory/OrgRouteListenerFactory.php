<?php

namespace Api\Factory;

use Api\Listeners\OrgRouteListener;
use Interop\Container\ContainerInterface;
use Org\Service\OrganizationServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class OrgRouteListenerFactory
 */
class OrgRouteListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new OrgRouteListener($container->get(OrganizationServiceInterface::class));
    }
}
