<?php

namespace Api\V1\Rest\Group;

use Group\Service\GroupServiceInterface;
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
        return new GroupResource($groupService);
    }
}
