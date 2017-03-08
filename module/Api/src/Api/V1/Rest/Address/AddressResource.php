<?php

namespace Api\V1\Rest\Address;

use Address\Address;
use Address\Service\AddressServiceInterface;
use Group\Service\GroupAddressServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class AddressResource
 */
class AddressResource extends AbstractResourceListener
{
    /**
     * @var AddressServiceInterface $addressService
     */
    protected $addressService;

    /**
     * @var GroupAddressServiceInterface $groupAddressService
     */
    protected $groupAddressService;

    /**
     * AddressResource constructor.
     * @param AddressServiceInterface $addressService
     * @param GroupAddressServiceInterface $groupAddressService
     */
    public function __construct(
        AddressServiceInterface $addressService,
        GroupAddressServiceInterface $groupAddressService
    ) {
        $this->addressService = $addressService;
        $this->groupAddressService = $groupAddressService;
    }

    /**
     * Fetch a address for a user
     *
     * The authenticated user must be allowed to fetch address
     *
     * @SWG\Get(
     *   path="/address/{address_id}",
     *   tags={"address"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="address_id",
     *     in="path",
     *     description="Address Id to be fetched",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Address was fetched",
     *     @SWG\Schema(ref="#/definitions/AddressEntity")
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Address not found",
     *     @SWG\Schema(ref="#/definitions/NotFoundError")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to access address",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  mixed $id
     *
     * @return ApiProblem|mixed
     */
    public function fetch($id)
    {
        return new AddressEntity(($this->addressService->fetchAddress($id))->getArrayCopy());
    }

    /**
     * Fetches multiple addresses the requested user has access too
     *
     * A User can request to view all addresses
     *
     * @SWG\Get(path="/address",
     *   tags={"address"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="postal_code",
     *     in="query",
     *     description="Postal/zip code of the address",
     *     type="string",
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="filter",
     *     in="query",
     *     description="type of entity requested",
     *     enum={"group"},
     *     type="string",
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
     *     description="Number of addresses on each page",
     *     type="integer",
     *     format="int32",
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Paged addresses",
     *     @SWG\Schema(ref="#/definitions/AddressCollection")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
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
        $prototype = new AddressEntity([]);

        $params = (array) $params;

        unset($params['page']);
        unset($params['per_page']);
        if (isset($params['filter']) && $params['filter'] === 'group') {
            unset($params['filter']);
            return new AddressCollection(
                $this->groupAddressService->fetchAddressesWithGroupsAttached($params, $prototype)
            );
        }

        return new AddressCollection($this->addressService->fetchAll($params, $prototype));
    }

    /**
     * Create a new address
     *
     * The authenticated user must be allowed to create a new address in the system
     *
     * @SWG\Post(path="/address",
     *   tags={"address"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Address data",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/Address")
     *   ),
     *   @SWG\Response(
     *     response=201,
     *     description="Address was created",
     *     @SWG\Schema(ref="#/definitions/AddressEntity")
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="Validation failed",
     *     @SWG\Schema(ref="#/definitions/ValidationError")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  mixed $data
     *
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $address = new Address((array)$data);
        $this->addressService->createAddress($address);
        return new AddressEntity($address->getArrayCopy());
    }

    /**
     * Update address
     *
     * @SWG\Put(path="/address/{address_id}",
     *   tags={"address"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="address_id",
     *     in="path",
     *     description="Address Id to update",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Address data",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/Address")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(ref="#/definitions/AddressEntity")
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="validation failed",
     *     @SWG\Schema(ref="#/definitions/ValidationError")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to update address",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  mixed $id
     * @param  mixed $data
     *
     * @return ApiProblem|mixed
     */
    public function update($id, $data)
    {
        $address = $this->addressService->fetchAddress($id);
        $address->exchangeArray((array)$data);

        $this->addressService->updateAddress($address);
        return new AddressEntity($address->getArrayCopy());
    }

    /**
     * Delete address
     *
     * A fetch is done first to ensure the user has access to a address.  The addresses are hard deleted.
     *
     * @SWG\Delete(path="/address/{address_id}",
     *   tags={"address"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="address_id",
     *     in="path",
     *     description="address id to be deleted",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=204,
     *     description="address was deleted",
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Address not found",
     *     @SWG\Schema(ref="#/definitions/NotFoundError")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to delete or access address",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=500,
     *     description="Problem occurred during execution",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  string $id
     *
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
        $address = $this->addressService->fetchAddress($id);
        if ($this->addressService->deleteAddress($address)) {
            return true;
        }

        return new ApiProblem(500, 'problem occured during address deletion');
    }
}
