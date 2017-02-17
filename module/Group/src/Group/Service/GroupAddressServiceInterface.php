<?php

namespace Group\Service;

use Address\AddressInterface;
use Application\Exception\NotFoundException;
use Group\GroupInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Interface GroupAddressServiceInterface
 */
interface GroupAddressServiceInterface
{
    /**
     * @param GroupInterface $group
     * @param AddressInterface $address
     * @return mixed
     */
    public function attachAddressToGroup(GroupInterface $group, AddressInterface $address) : bool;

    /**
     * @param GroupInterface $group
     * @param AddressInterface $address
     * @return mixed
     */
    public function detachAddressFromGroup(GroupInterface $group, AddressInterface $address) : bool;

    /**
     * @param GroupInterface $group
     * @param null $where
     * @param AddressInterface | null $prototype
     * @return AdapterInterface
     */
    public function fetchAllAddressesForGroup(
        GroupInterface $group,
        $where = null,
        AddressInterface $prototype = null
    ) : AdapterInterface;

    /**
     * @param GroupInterface $group
     * @param AddressInterface $address
     * @return AddressInterface
     * @throws NotFoundException
     */
    public function fetchAddressForGroup(GroupInterface $group, AddressInterface $address) : AddressInterface;

    /**
     * @param $where
     * @param GroupInterface|null $prototype
     * @return AdapterInterface
     */
    public function fetchAllGroupsInAddress($where, GroupInterface $prototype = null) : AdapterInterface;
}
