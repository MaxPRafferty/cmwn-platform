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
        $groupService = $services->get('Group\Service');

        /** @var OrganizationServiceInterface $orgService */
        $orgService = $services->get('Org\Service');
        return new GroupResource($groupService, $orgService);
    }
}
