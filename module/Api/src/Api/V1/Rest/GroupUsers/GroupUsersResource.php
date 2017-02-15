<?php
namespace Api\V1\Rest\GroupUsers;

use Api\V1\Rest\User\UserEntity;
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
     * Fetches the users in the group requested user has access to
     *
     * A user can request to see all the users in a group which he has access to
     * @SWG\Get(path="/group/{group_id}/user",
     *   tags={"user-game"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="group_id",
     *     in="path",
     *     description="Group Id of the group",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number to fetch",
     *     type="integer",
     *     format="int32",
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="per_page",
     *     in="query",
     *     description="Number of games on each page",
     *     type="integer",
     *     format="int32",
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Paged users",
     *     @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref="#/definitions/UserCollection")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Group not found",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/NotFoundError")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/Error")
     *     )
     *   )
     * )
     * @param  array $params
     *
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        $groupId = $this->getEvent()->getRouteParam('group_id');


        $group = $this->groupService->fetchGroup($groupId);


        return new GroupUsersCollection($this->userGroupService->fetchUsersForGroup($group, [], new UserEntity()));
    }

    /**
     * Attach a user to a group
     *
     * The authenticated user must be allowed to attach a user to a group in the system
     *
     * @SWG\Post(path="/group/{group_id}/user/{user_id}",
     *   tags={"user-group"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="group_id",
     *     in="path",
     *     description="Game Id to deleted",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="User Id of the user",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Group Role",
     *     required=true,
     *     @SWG\Property(
     *         type="string",
     *         property="role",
     *         readOnly=true,
     *         description="The role of the user in group"
     *      )
     *   ),
     *   @SWG\Response(
     *     response=201,
     *     description="User attached to group",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/UserEntity")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to attach user to group",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/Error")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/Error")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=500,
     *     description="Problem occurred while attaching user to group",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/Error")
     *     )
     *   )
     * )
     * @param  mixed $data
     *
     * @return ApiProblem|mixed
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
     * Detach a game from a user
     *
     * A fetch is done first to ensure the user has access to a game. The authenticated user will get a 403
     * if the they are not allowed to detach a game from a user
     *
     * @SWG\Delete(path="/group/{group_id}/user/{user_id}",
     *   tags={"user-game"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="group_id",
     *     in="path",
     *     description="Group Id to deleted",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="User Id of the user",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=204,
     *     description="User was detached from group",
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Group or user not found",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/NotFoundError")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to detach or access user or group",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/Error")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/Error")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=500,
     *     description="Problem occurred during execution",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/Error")
     *     )
     *   )
     * )
     * @param  string $userId
     *
     * @return ApiProblem|mixed
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
