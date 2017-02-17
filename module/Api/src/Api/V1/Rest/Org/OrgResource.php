<?php
namespace Api\V1\Rest\Org;

use Org\Service\OrganizationServiceInterface;
use Zend\Hydrator\ClassMethods;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * A Resource for dealing with organizations
 */
class OrgResource extends AbstractResourceListener
{
    /**
     * @var OrganizationServiceInterface
     */
    protected $service;

    /**
     * @var ClassMethods
     */
    protected $hydrator;

    /**
     * OrgResource constructor.
     *
     * @param OrganizationServiceInterface $service
     */
    public function __construct(OrganizationServiceInterface $service)
    {
        $this->service  = $service;
        $this->hydrator = new ClassMethods();
    }

    /**
     * Create an Organization
     *
     * This allows a user to create an organization if they are allowed too
     *
     * @SWG\Post(path="/org",
     *   tags={"organization"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Organization data",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/Organization")
     *   ),
     *   @SWG\Response(
     *     response=201,
     *     description="successful operation",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/OrgEntity"
     *     )
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="validation failed",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/ValidationError"
     *     )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/Error"
     *     )
     *   )
     * )
     * @param  mixed $data
     *
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $org  = new OrgEntity();
        $data = (array)$data;
        unset($data['org_id']); // @paranoid make sure we do not try to overwrite a different org
        $this->hydrator->hydrate($data, $org);
        $this->service->createOrganization($org);

        return $org;
    }

    /**
     * Delete an Organization
     *
     * This will soft delete a organization by default unless the hard parameter is set in the query.  If the user
     * is not allowed to soft or hard delete, a 403 will be thrown
     *
     * @SWG\Delete(path="/org/{org_id}",
     *   tags={"organization"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="org_id",
     *     in="path",
     *     description="Id of the organization to delete",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     minimum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="hard",
     *     in="query",
     *     description="Hard delete the organization",
     *     type="boolean",
     *     minimum=1.0,
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Organization that was deleted",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/OrgEntity"
     *     )
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Organization not found",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/NotFoundError"
     *     )
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to access or delete the organization",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/Error"
     *     )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/Error"
     *     )
     *   )
     * )
     * @param  mixed $orgId
     *
     * @todo Add Hard delete flag
     * @return ApiProblem|bool
     */
    public function delete($orgId)
    {
        $org = $this->fetch($orgId);

        if ($this->service->deleteOrganization($org)) {
            return true;
        }

        return new ApiProblem(500, 'Error deleting organization', 'Internal Server Error');
    }

    /**
     * Fetch an Organization
     *
     * Returns information about a specific organization the user has access too.  403 is thrown if the user is not
     * allowed to access this organization
     *
     * @SWG\Get(path="/org/{org_id}",
     *   tags={"organization"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="org_id",
     *     in="path",
     *     description="Id of the Organization to fetch",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     minimum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/OrgCollection"
     *     )
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Org not found",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/NotFoundError"
     *     )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/Error"
     *     )
     *   )
     * )
     * @param  mixed $orgId
     *
     * @return ApiProblem|mixed
     */
    public function fetch($orgId)
    {
        return $this->service->fetchOrganization($orgId, new OrgEntity());
    }

    /**
     * Fetches multiple organizations
     *
     * This will fetch all organizations the user has access too.  If the user cannot fetch a list of orgaanizations
     * a 403 will be returned
     *
     * @SWG\Get(path="/org",
     *   tags={"organization"},
     *   x={"prime-for":"org"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     description="Type of organizations to fetch",
     *     type="string",
     *     enum={"school","district","class","generic"},
     *     minimum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number to fetch",
     *     type="integer",
     *     format="int32",
     *     minimum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="per_page",
     *     in="query",
     *     description="Number of organizations to list on each page",
     *     type="integer",
     *     format="int32",
     *     minimum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="The current page of organizations",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/OrgCollection"
     *     )
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Org not found",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/NotFoundError"
     *     )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/Error"
     *     )
     *   )
     * )
     * @param  array $params
     *
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        return new OrgCollection($this->service->fetchAll($params, new OrgEntity()));
    }

    /**
     * Update an organization
     *
     * A check is done to ensure the user has access to the organization in question.  A 404/403 is thrown if the user
     * is not allowed access or denied access to the organization
     *
     * @SWG\Put(path="/org/{org_id}",
     *   tags={"organization"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="org_id",
     *     in="path",
     *     description="Id of the organization to update",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     minimum=1.0,
     *     maximum=1.0,
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Organization data",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/Organization")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/OrgEntity"
     *     )
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="validation failed",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/ValidationError"
     *     )
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to create a org",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/Error"
     *     )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(
     *          type="object",
     *          ref="#/definitions/Error"
     *     )
     *   )
     * )
     * @param  mixed $orgId
     * @param  mixed $data
     *
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

        if ($this->service->updateOrganization($org)) {
            return $org;
        }

        return new ApiProblem(500, 'Failed to update organization', 'Internal Server Error');
    }
}
