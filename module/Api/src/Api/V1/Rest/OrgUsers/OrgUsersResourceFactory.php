<?php

namespace Api\V1\Rest\OrgUsers;

use Group\Service\UserGroupService;
use Group\Service\UserGroupServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class OrgUsersResourceFactory
 * @package Api\V1\Rest\OrgUsers
 */
class OrgUsersResourceFactory implements FactoryInterface
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
        $userGroupService = $services->get(UserGroupServiceInterface::class);
        return new OrgUsersResource($userGroupService);
    }
}
