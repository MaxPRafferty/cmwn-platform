<?php
namespace Api\V1\Rest\Org;

use Org\Organization;
use Org\OrganizationInterface;
use Org\Service\OrganizationServiceInterface;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Where;
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
     * OrgResource constructor.
     * @param OrganizationServiceInterface $service
     */
    public function __construct(OrganizationServiceInterface $service)
    {
        $this->service     = $service;
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
        $org = new Organization($data);

        $this->service->createOrganization($org);
        return new OrgEntity($org->getArrayCopy());
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
        $org = $this->getEvent()->getRouteParam('org', false);
        
        if ($org instanceof OrganizationInterface) {
            return new OrgEntity($org->getArrayCopy());
        }

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
        $query = new Where(); // TODO create filter spec
        if (isset($params['type'])) {
            $query->addPredicate(new Operator('o.type', '=', $params['type']));
        }

        $orgs = $this->service->fetchAll($query, true, new OrgEntity());
        return new OrgCollection($orgs);
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
