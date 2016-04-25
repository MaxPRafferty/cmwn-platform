<?php

namespace Api\V1\Rest\GroupUsers;

use Group\Service\UserGroupServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class GroupUsersResourceFactory
 * @package Api\V1\Rest\GroupUsers
 */
class GroupUsersResourceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $services)
    {
        /** @var UserGroupServiceInterface $userGroupService */
        $userGroupService = $services->get(UserGroupServiceInterface::class);
        return new GroupUsersResource($userGroupService);
    }
}
