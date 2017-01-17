<?php

namespace Api\V1\Rest\GroupUsers;

use Group\Service\GroupServiceInterface;
use Group\Service\UserGroupServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class GroupUsersResourceFactory
 */
class GroupUsersResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new GroupUsersResource(
            $container->get(UserGroupServiceInterface::class),
            $container->get(GroupServiceInterface::class)
        );
    }
}
