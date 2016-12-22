<?php

namespace Security\Factory;

use Group\Service\UserGroupServiceInterface;
use Interop\Container\ContainerInterface;
use Security\Listeners\GroupServiceListener;
use Zend\ServiceManager\Factory\FactoryInterface;
use Security\Service\SecurityOrgServiceInterface;

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
            $container->get(SecurityOrgServiceInterface::class)
        );
    }
}
