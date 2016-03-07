<?php
namespace Api\V1\Rest\GroupUsers;

use Api\V1\Rest\User\UserEntity;
use Group\Service\UserGroupServiceInterface;
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
        return new GroupUsersCollection($this->groupService->fetchUsersForGroup($groupId, new UserEntity()));
    }
}
