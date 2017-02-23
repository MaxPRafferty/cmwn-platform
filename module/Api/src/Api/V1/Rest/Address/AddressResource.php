<?php

namespace Api\V1\Rest\Address;

use Address\Address;
use Address\Service\AddressServiceInterface;
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
     * AddressResource constructor.
     * @param AddressServiceInterface $addressService
     */
    public function __construct(AddressServiceInterface $addressService)
    {
        $this->addressService = $addressService;
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
        return new AddressCollection($this->addressService->fetchAll(null, new AddressEntity([])));
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
