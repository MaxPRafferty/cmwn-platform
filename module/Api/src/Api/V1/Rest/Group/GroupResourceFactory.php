<?php

namespace Api\V1\Rest\Group;

use Group\Service\GroupServiceInterface;
use Org\Service\OrganizationServiceInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class GroupResourceFactory
 * @package Api\V1\Rest\Group
 */
class GroupResourceFactory
{
    /**
     * @param ServiceLocatorInterface $services
     * @return GroupResource
     */
    public function __invoke(ServiceLocatorInterface $services)
    {
        /** @var GroupServiceInterface $groupService */
        /** @var OrganizationServiceInterface $orgService */
        $groupService = $services->get(GroupServiceInterface::class);
        $orgService   = $services->get(OrganizationServiceInterface::class);
        return new GroupResource($groupService, $orgService);
    }
}
