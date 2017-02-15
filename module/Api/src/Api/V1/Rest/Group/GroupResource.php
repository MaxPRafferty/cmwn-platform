<?php
namespace Api\V1\Rest\Group;

use Api\V1\Rest\Org\OrgEntity;
use Group\Group;
use Group\Service\GroupServiceInterface;
use Org\Service\OrganizationServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Resource for dealing with groups
 */
class GroupResource extends AbstractResourceListener
{
    /**
     * @var GroupServiceInterface
     */
    protected $service;

    /**
     * @var OrganizationServiceInterface
     */
    protected $orgService;

    /**
     * GroupResource constructor.
     *
     * @param GroupServiceInterface $service
     * @param OrganizationServiceInterface $orgService
     */
    public function __construct(GroupServiceInterface $service, OrganizationServiceInterface $orgService)
    {
        $this->service    = $service;
        $this->orgService = $orgService;
    }

    /**
     * Create a new group
     *
     * The authenticated user must be allowed to create a new group in the system
     *
     * @SWG\Post(path="/group",
     *   tags={"group"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Group data",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/Group")
     *   ),
     *   @SWG\Response(
     *     response=201,
     *     description="Group was created",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/GroupEntity")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="Validation failed",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/ValidationError")
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
     * @param  mixed $data
     *
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $group = new GroupEntity();
        $data  = $this->getInputFilter()->getValues();
        foreach ($data as $key => $value) {
            $group->__set($key, $value);
        }

        $this->service->createGroup($group);

        return $group;
    }

    /**
     * Delete a group
     *
     * A fetch is done first to ensure the user has access to a group.  By default groups are soft deleted unless
     * the "hard" parameter is set in the query.  The authenticated user will get a 403 if the they are not allowed
     * to hard delete a group
     *
     * @SWG\Delete(path="/group/{group_id}",
     *   tags={"group"},
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
     *     name="hard",
     *     in="query",
     *     description="Hard delete the group",
     *     type="boolean",
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Group was deleted",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/GroupEntity")
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
     *     response=403,
     *     description="Not Authorized to delete or access group",
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
     *   )
     * )
     * @param  string $groupId
     *
     * @return ApiProblem|mixed
     */
    public function delete($groupId)
    {
        $group = $this->fetch($groupId);

        if ($this->service->deleteGroup($group)) {
            return true;
        }

        return new ApiProblem(500, 'Failed to delete group');
    }

    /**
     * Fetch data for a group
     *
     * Fetch the data for a group if the authenticated user is allowed access.
     *
     * @SWG\Get(path="/group/{group_id}",
     *   tags={"group"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="group_id",
     *     in="path",
     *     description="Group Id to fetch",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="The requested group",
     *     @SWG\Schema(ref="#/definitions/GroupCollection")
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
     * @param  mixed $groupId
     *
     * @return ApiProblem|GroupEntity
     */
    public function fetch($groupId)
    {
        /** @var GroupEntity $group */
        $group = $this->service->fetchGroup($groupId, new GroupEntity());
        $group->attachToOrganization(
            $this->orgService->fetchOrganization($group->getOrganizationId(), new OrgEntity())
        );

        $parent = null;
        if ($group->getParentId() !== null) {
            $group->attachToGroup($this->service->fetchGroup($group->getParentId(), new GroupEntity()));
        }

        return $group;
    }

    /**
     * Fetches multiple groups the authenticated user has access too
     *
     * A User can request all groups or all the child groups descending from parent.  Empty results are returned if the
     * user is not allowed access to a parent
     *
     * @SWG\Get(path="/group",
     *   tags={"group"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     description="Type of group to fetch",
     *     type="string",
     *     enum={"district","school","class","generic"},
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="parent",
     *     in="query",
     *     description="Fetch all the children of this group",
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
     *     description="Number of groups on each page",
     *     type="integer",
     *     format="int32",
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Paged groups",
     *     @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref="#/definitions/GroupCollection")
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
        $params = (array)$params;
        if (!isset($params['parent'])) {
            return new GroupCollection($this->service->fetchAll($params, new GroupEntity()));
        }

        $parentGroup = $this->fetch($params['parent']);
        unset($params['parent']);
        $groups = $this->service->fetchChildGroups($parentGroup, $params, new GroupEntity());

        return new GroupCollection($groups);
    }

    /**
     * Update a group
     *
     * The user must be allowed access to the group and be allowed to edit groups.  403 is returned if the user is not
     * allowed access to update the group. 404 is returned if the group is not found or the user is not allowed access
     *
     * @SWG\Put(path="/group/{group_id}",
     *   tags={"group"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="group_id",
     *     in="path",
     *     description="Group Id to update",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Group data",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/Group")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/GroupEntity")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="validation failed",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/ValidationError")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to update a group",
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
     *   )
     * )
     * @param  mixed $groupId
     * @param  mixed $data
     *
     * @return ApiProblem|mixed
     */
    public function update($groupId, $data)
    {
        $group     = $this->fetch($groupId);
        $data      = $this->getInputFilter()->getValues();
        $saveGroup = new Group(array_merge($group->getArrayCopy(), $data));
        $this->service->updateGroup($saveGroup);

        return $group;
    }
}
