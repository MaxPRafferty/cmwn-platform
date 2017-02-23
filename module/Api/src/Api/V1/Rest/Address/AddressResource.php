<?php

namespace Api\V1\Rest\Address;

use Address\Address;
use Address\Service\AddressServiceInterface;
use Group\Service\GroupAddressServiceInterface;
use Zend\Db\Sql\Predicate\Operator;
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
     * @SWG\Get(path="/address/{address_id}",
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
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/AddressEntity")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Address not found",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/NotFoundError")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to access address",
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
     *     @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref="#/definitions/AddressCollection")
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
     *     response=403,
     *     description="Not Authorized",
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
        $where = null;
        $prototype = new AddressEntity([]);

        if (isset($params['postal_code'])) {
            $where = new Operator('postal_code', Operator::OP_EQ, $params['postal_code']);
        }

        if (isset($params['filter']) && $params['filter'] === 'group') {
            return new AddressCollection(
                $this->groupAddressService->fetchAddressesWithGroupsAttached($where, $prototype)
            );
        }

        return new AddressCollection($this->addressService->fetchAll($where, $prototype));
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
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/AddressEntity")
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
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized",
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
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/AddressEntity")
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
     *     description="Not Authorized to update address",
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
     *     response=200,
     *     description="address was deleted",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/AddressEntity")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Address not found",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/NotFoundError")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to delete or access address",
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
     * @param  string $id
     *
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
        $address = $this->addressService->fetchAddress($id);
        $this->addressService->deleteAddress($address);
        return new ApiProblem(200, 'Deleted, Ok.');
    }
}
