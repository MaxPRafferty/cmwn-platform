<?php
namespace Api\V1\Rest\Group;

use Group\Group;
use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use Group\Service\UserGroupServiceInterface;
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
     * GroupResource constructor.
     *
     * @param GroupServiceInterface $service
     */
    public function __construct(GroupServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $data = (array) $data;
        unset($data['group_id']);
        $group = new Group($data);

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
     * @return ApiProblem|mixed
     */
    public function fetch($groupId)
    {
        $group  = $this->getEvent()->getRouteParam('group', false);

        if ($group instanceof GroupInterface) {
            return new GroupEntity($group->getArrayCopy());
        }

        return new GroupEntity($this->service->fetchGroup($groupId)->getArrayCopy());
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
            $query['type'] = $params['type'];
        }

        if (isset($params['org_id'])) {
            $query['organization_id'] = $params['org_id'];
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
        $data = $this->getInputFilter()->getValues();

        $data['group_id'] = $groupId;
        foreach ($data as $key => $value) {
            $group->__set($key, $value);
        }

        $this->service->saveGroup($group);
        return $group;
    }
}
