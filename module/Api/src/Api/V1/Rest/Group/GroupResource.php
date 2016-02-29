<?php
namespace Api\V1\Rest\Group;

use Group\Group;
use Group\Service\GroupServiceInterface;
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
     * Delete a collection, or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function deleteList($data)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for collections');
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $groupId
     * @return ApiProblem|mixed
     */
    public function fetch($groupId)
    {
        return new GroupEntity($this->service->fetchGroup($groupId)->getArrayCopy());
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = array())
    {
        /** @var DbSelect $groups */
        $groups = $this->service->fetchAll(null, true, new GroupEntity());
        return new GroupCollection($groups);

    }

    /**
     * Patch (partial in-place update) a resource
     *
     * @param  mixed $groupId
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function patch($groupId, $data)
    {
        return new ApiProblem(405, 'The PATCH method has not been defined for individual resources');
    }

    /**
     * Replace a collection or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function replaceList($data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for collections');
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
