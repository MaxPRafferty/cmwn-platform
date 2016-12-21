<?php

namespace Api\Factory;

use Api\Listeners\GroupRouteListener;
use Group\Service\GroupServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Security\Service\SecurityOrgService;

/**
 * Class GroupRouteListenerFactory
 */
class GroupRouteListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new GroupRouteListener(
            $container->get(GroupServiceInterface::class),
            $container->get(SecurityOrgService::class)
        );
    }
}
