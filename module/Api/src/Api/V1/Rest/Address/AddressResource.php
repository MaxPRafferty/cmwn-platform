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
     * @inheritdoc
     */
    public function fetch($id)
    {
        return new AddressEntity(($this->addressService->fetchAddress($id))->getArrayCopy());
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($params = [])
    {
        $where = null;
        $prototype = new AddressEntity([]);

        if (isset($params['postal_code'])) {
            $where = ['postal_code' => $params['postal_code']];
        }

        if (isset($params['filter']) && $params['filter'] === 'group') {
            return new AddressCollection(
                $this->groupAddressService->fetchAddressesWithGroupsAttached($where, $prototype)
            );
        }

        return new AddressCollection($this->addressService->fetchAll($where, $prototype));
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $address = new Address((array)$data);
        $this->addressService->createAddress($address);
        return new AddressEntity($address->getArrayCopy());
    }

    /**
     * @inheritdoc
     */
    public function update($id, $data)
    {
        $address = $this->addressService->fetchAddress($id);
        $address->exchangeArray((array)$data);

        $this->addressService->updateAddress($address);
        return new AddressEntity($address->getArrayCopy());
    }

    /**
     * @inheritdoc
     */
    public function delete($id)
    {
        $address = $this->addressService->fetchAddress($id);
        $this->addressService->deleteAddress($address);
        return new ApiProblem(200, 'Deleted, Ok.');
    }
}
