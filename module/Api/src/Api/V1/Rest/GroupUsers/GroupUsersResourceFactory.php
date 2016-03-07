<?php

namespace Api\V1\Rest\GroupUsers;

use Group\Service\UserGroupService;
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
        /** @var UserGroupService $userGroupService */
        $userGroupService = $services->get('Group\Service\UserGroupService');
        return new GroupUsersResource($userGroupService);
    }
}
