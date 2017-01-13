<?php

namespace Api\V1\Rest\GroupAddress;

use Address\Service\AddressServiceInterface;
use Address\Service\GroupAddressServiceInterface;
use Api\V1\Rest\Address\AddressCollection;
use Api\V1\Rest\Address\AddressEntity;
use Group\Service\GroupServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class GroupAddressResource
 * @package Api\V1\Rest\GroupAddress
 */
class GroupAddressResource extends AbstractResourceListener
{
    /**
     * @var AddressServiceInterface
     */
    protected $addressService;

    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var GroupAddressServiceInterface
     */
    protected $groupAddressService;

    /**
     * GroupAddressResource constructor.
     * @param GroupAddressServiceInterface $groupAddressService
     * @param AddressServiceInterface $addressService
     * @param GroupServiceInterface $groupService
     */
    public function __construct(
        GroupAddressServiceInterface $groupAddressService,
        AddressServiceInterface $addressService,
        GroupServiceInterface $groupService
    ) {
        $this->groupAddressService = $groupAddressService;
        $this->addressService = $addressService;
        $this->groupService = $groupService;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($params = [])
    {
        $groupId = $this->getEvent()->getRouteParam('group_id');
        $group = $this->groupService->fetchGroup($groupId);
        return new AddressCollection($this->groupAddressService->fetchAllAddressesForGroup(
            $group,
            null,
            new AddressEntity([])
        ));
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $groupId = $this->getEvent()->getRouteParam('group_id');
        $addressId = $this->getEvent()->getRouteParam('address_id');

        $group = $this->groupService->fetchGroup($groupId);
        $address = $this->addressService->fetchAddress($addressId);

        $this->groupAddressService->attachAddressToGroup($group, $address);
        return new ApiProblem(201, "Address attached to group successfully");
    }

    /**
     * @inheritdoc
     */
    public function delete($addressId)
    {
        $groupId = $this->getEvent()->getRouteParam('group_id');
        $group = $this->groupService->fetchGroup($groupId);
        $address = $this->addressService->fetchAddress($addressId);

        $this->groupAddressService->detachAddressFromGroup($group, $address);
        return new ApiProblem(200, "Address attached to group successfully");
    }
}
