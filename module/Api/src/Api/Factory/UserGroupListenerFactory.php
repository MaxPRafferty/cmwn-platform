<?php

namespace Api\Factory;

use Api\Listeners\UserGroupListener;
use Group\Service\UserGroupServiceInterface;
use Interop\Container\ContainerInterface;
use Org\Service\OrganizationServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class UserGroupListenerFactory
 */
class UserGroupListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UserGroupListener(
            $container->get(UserGroupServiceInterface::class),
            $container->get(OrganizationServiceInterface::class)
        );
    }
}
