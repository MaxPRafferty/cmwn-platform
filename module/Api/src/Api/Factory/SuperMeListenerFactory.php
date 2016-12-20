<?php

namespace Api\Factory;

use Api\Listeners\SuperMeListener;
use Group\Service\GroupServiceInterface;
use Interop\Container\ContainerInterface;
use Org\Service\OrganizationServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class SuperMeListenerFactory
 */
class SuperMeListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SuperMeListener(
            $container->get(GroupServiceInterface::class),
            $container->get(OrganizationServiceInterface::class)
        );
    }
}
