<?php
namespace Api\V1\Rest\GroupUsers;

use Api\V1\Rest\User\UserEntity;
use Group\GroupInterface;
use Group\Service\UserGroupServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class GroupUsersResource
 * @package Api\V1\Rest\GroupUsers
 */
class GroupUsersResource extends AbstractResourceListener
{
    /**
     * @var UserGroupServiceInterface
     */
    protected $groupService;

    /**
     * GroupUsersResource constructor.
     * @param UserGroupServiceInterface $groupService
     */
    public function __construct(UserGroupServiceInterface $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * @param mixed $groupId
     * @return GroupUsersCollection
     */
    public function fetch($groupId)
    {
        $group = $this->getEvent()->getRouteParam('group');
        if (!$group instanceof GroupInterface) {
            return new ApiProblem(421, 'Routing error');
        }

        return new GroupUsersCollection($this->groupService->fetchUsersForGroup($group, new UserEntity()));
    }
}
