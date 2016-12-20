<?php

namespace Security\Factory;

use Group\Service\UserGroupServiceInterface;
use Interop\Container\ContainerInterface;
use Security\Listeners\GroupServiceListener;
use Security\Service\SecurityOrgService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class GroupServiceListenerFactory
 */
class GroupServiceListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new GroupServiceListener(
            $container->get(UserGroupServiceInterface::class),
            $container->get(SecurityOrgService::class)
        );
    }
}
