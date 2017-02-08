<?php
namespace Api\V1\Rest\GroupUsers;

use Api\V1\Rest\User\UserEntity;
use Application\Exception\NotFoundException;
use Group\Service\GroupServiceInterface;
use Group\Service\UserGroupServiceInterface;
use User\Service\UserServiceInterface;
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
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * GroupUsersResource constructor.
     * @param UserGroupServiceInterface $userGroupService
     * @param GroupServiceInterface $groupService
     * @param UserServiceInterface $userService
     */
    public function __construct(
        UserGroupServiceInterface $userGroupService,
        GroupServiceInterface $groupService,
        UserServiceInterface $userService
    ) {
        $this->userGroupService = $userGroupService;
        $this->groupService = $groupService;
        $this->userService = $userService;
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

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $groupId = $this->getEvent()->getRouteParam('group_id');
        $userId = $this->getEvent()->getRouteParam('user_id');
        $data = (array) $data;
        $role = $data['role'];
        $group = $this->groupService->fetchGroup($groupId);
        $user = $this->userService->fetchUser($userId);
        if ($this->userGroupService->attachUserToGroup($group, $user, $role)) {
            return new UserEntity($user->getArrayCopy());
        }

        return new ApiProblem(500, 'Problem attaching user to group');
    }

    /**
     * @inheritdoc
     */
    public function delete($userId)
    {
        $groupId = $this->getEvent()->getRouteParam('group_id');
        $group = $this->groupService->fetchGroup($groupId);
        $user = $this->userService->fetchUser($userId);
        if ($this->userGroupService->detachUserFromGroup($group, $user)) {
            return true;
        }
        return new ApiProblem(500, 'Problem removing user from group');
    }
}
