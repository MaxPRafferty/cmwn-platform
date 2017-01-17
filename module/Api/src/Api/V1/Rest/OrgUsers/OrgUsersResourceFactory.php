<?php

namespace Api\V1\Rest\OrgUsers;

use Group\Service\UserGroupServiceInterface;
use Interop\Container\ContainerInterface;
use Org\Service\OrganizationServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class OrgUsersResourceFactory
 */
class OrgUsersResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new OrgUsersResource(
            $container->get(UserGroupServiceInterface::class),
            $container->get(OrganizationServiceInterface::class)
        );
    }
}
