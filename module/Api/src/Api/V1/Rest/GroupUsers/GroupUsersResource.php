<?php
namespace Api\V1\Rest\GroupUsers;

use Api\V1\Rest\User\UserEntity;
use Application\Exception\NotFoundException;
use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
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
    protected $userGroupService;

    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * GroupUsersResource constructor.
     * @param UserGroupServiceInterface $userGroupService
     * @param GroupServiceInterface $groupService
     */
    public function __construct(UserGroupServiceInterface $userGroupService, GroupServiceInterface $groupService)
    {
        $this->userGroupService = $userGroupService;
        $this->groupService = $groupService;
    }

    /**
     * @param mixed $groupId
     * @return GroupUsersCollection
     */
    public function fetch($groupId)
    {
        try {
            $group = $this->groupService->fetchGroup($groupId);
        } catch (NotFoundException $notFound) {
            return new ApiProblem(421, 'Routing error');
        }

        return new GroupUsersCollection($this->userGroupService->fetchUsersForGroup($group, [], new UserEntity()));
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($params = [])
    {
        $groupId = $this->getEvent()->getRouteParam('group_id');


        $group = $this->groupService->fetchGroup($groupId);


        return new GroupUsersCollection($this->userGroupService->fetchUsersForGroup($group, [], new UserEntity()));
    }
}
