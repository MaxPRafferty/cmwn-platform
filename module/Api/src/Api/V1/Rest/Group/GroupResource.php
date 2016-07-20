<?php
namespace Api\V1\Rest\Group;

use Group\Group;
use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use Org\Service\OrganizationServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class GroupResource
 * @package Api\V1\Rest\Group
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
     * @param GroupServiceInterface $service
     * @param OrganizationServiceInterface $orgService
     */
    public function __construct(GroupServiceInterface $service, OrganizationServiceInterface $orgService)
    {
        $this->service    = $service;
        $this->orgService = $orgService;
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $group = new Group($this->getInputFilter()->getValues());
        $this->service->saveGroup($group);
        return new GroupEntity($group->getArrayCopy());
    }

    /**
     * Delete a resource
     *
     * @param  mixed $groupId
     * @return ApiProblem|mixed
     */
    public function delete($groupId)
    {
        $group = $this->fetch($groupId);

        $this->service->deleteGroup($group);
        return new ApiProblem(200, 'Group deleted', 'Ok');
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $groupId
     * @return ApiProblem|GroupEntity
     */
    public function fetch($groupId)
    {
        $group = $this->getEvent()->getRouteParam('group', false);
        $group = !$group instanceof GroupInterface ? $this->service->fetchGroup($groupId) : $group;

        $org = $this->orgService->fetchOrganization($group->getOrganizationId());

        $parent = null;
        if ($group->getParentId() !== null) {
            $parent = $this->service->fetchGroup($group->getParentId());
        }

        return new GroupEntity($group->getArrayCopy(), $org, $parent);
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        $query  = [];
        if (isset($params['type'])) {
            $query['g.type'] = $params['type'];
        }

        if (isset($params['org_id'])) {
            $query['g.organization_id'] = $params['org_id'];
        }

        if (!isset($params['parent'])) {
            return new GroupCollection($this->service->fetchAll($query, true, new GroupEntity()));
        }

        $parentGroup = $this->fetch($params['parent']);
        $groups      = $this->service->fetchChildGroups($parentGroup, $query, new GroupEntity());
        return new GroupCollection($groups);
    }

    /**
     * Update a resource
     *
     * @param  mixed $groupId
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function update($groupId, $data)
    {
        $group = $this->fetch($groupId);
        $data  = $this->getInputFilter()->getValues();

        $saveGroup = new Group(array_merge($group->getArrayCopy(), $data));
        $this->service->saveGroup($saveGroup);
        return $group;
    }
}
