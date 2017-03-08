<?php

namespace Api\V1\Rest\AddressGroup;

use Api\V1\Rest\Group\GroupCollection;
use Api\V1\Rest\Group\GroupEntity;
use Group\Service\GroupAddressServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class AddressGroupResource
 * @package Api\V1\Rest\AddressGroup
 */
class AddressGroupResource extends AbstractResourceListener
{
    /**
     * @var GroupAddressServiceInterface
     */
    protected $groupAddressService;

    /**
     * AddressGroupResource constructor.
     * @param GroupAddressServiceInterface $groupAddressService
     */
    public function __construct(GroupAddressServiceInterface $groupAddressService)
    {
        $this->groupAddressService = $groupAddressService;
    }

    /**
     * Fetches multiple groups in the requested address
     *
     * This resource allows a super user to view all the groups in a given address
     *
     * @SWG\Get(path="/address/{address_id}/group",
     *   tags={"user-game"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="address_id",
     *     in="path",
     *     description="Address Id of the address",
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
     *     description="Number of groups on each page",
     *     type="integer",
     *     format="int32",
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Paged groups in address",
     *     @SWG\Schema(ref="#/definitions/GroupCollection")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *  @SWG\Response(
     *     response=403,
     *     description="Not Authorized",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  array $params
     *
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        $params = (array) $params;
        unset($params['page']);
        unset($params['per_page']);
        $params['address_id'] = $this->getEvent()->getRouteParam('address_id');

        return new GroupCollection($this->groupAddressService->fetchAllGroupsInAddress($params, new GroupEntity()));
    }
}
