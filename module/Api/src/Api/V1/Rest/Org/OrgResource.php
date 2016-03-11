<?php
namespace Api\V1\Rest\Org;

use Org\Organization;
use Org\Service\OrganizationServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class OrgResource
 *
 * @package Api\V1\Rest\Org
 */
class OrgResource extends AbstractResourceListener
{
    /**
     * @var OrganizationServiceInterface
     */
    protected $service;

    /**
     * orgResource constructor.
     * @param OrganizationServiceInterface $service
     */
    public function __construct(OrganizationServiceInterface $service)
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
        unset($data['org_id']);
        $org = new OrgEntity($data);

        $this->service->createOrganization($org);
        return $org;
    }

    /**
     * Delete a resource
     *
     * @param  mixed $orgId
     * @return ApiProblem|mixed
     */
    public function delete($orgId)
    {
        $org = $this->fetch($orgId);

        $this->service->deleteOrganization($org);
        return new ApiProblem(200, 'Organization deleted', 'Ok');
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $orgId
     * @return ApiProblem|mixed
     */
    public function fetch($orgId)
    {
        return new OrgEntity($this->service->fetchOrganization($orgId)->getArrayCopy());
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        $orgs = $this->service->fetchAll(null, true, new OrgEntity());
        return new OrgCollection($orgs);
    }

    /**
     * Patch (partial in-place update) a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function patch($id, $data)
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
     * @param  mixed $orgId
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function update($orgId, $data)
    {
        $org  = $this->fetch($orgId);
        $data = $this->getInputFilter()->getValues();

        $data['org_id'] = $orgId;
        foreach ($data as $key => $value) {
            $org->__set($key, $value);
        }

        $this->service->saveOrg($org);
        return $org;
    }
}
