<?php

namespace Group\Service;

use Address\AddressInterface;
use Application\Exception\NotFoundException;
use Group\GroupInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Interface GroupAddressServiceInterface
 * @package Address\Service
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
     * @return DbSelect
     */
    public function fetchAllAddressesForGroup(
        GroupInterface $group,
        $where = null,
        AddressInterface $prototype = null
    ) : DbSelect;

    /**
     * @param GroupInterface $group
     * @param AddressInterface $address
     * @return array
     * @throws NotFoundException
     */
    public function fetchAddressForGroup(GroupInterface $group, AddressInterface $address);

    /**
     * @param $where
     * @param GroupInterface|null $prototype
     * @return DbSelect
     */
    public function fetchAllGroupsInAddress($where, GroupInterface $prototype = null);
}
