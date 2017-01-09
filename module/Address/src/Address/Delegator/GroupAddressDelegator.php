<?php

namespace Address\Delegator;

use Address\AddressInterface;
use Address\Service\GroupAddressServiceInterface;
use Group\GroupInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class GroupAddressDelegator
 * @package Address\Delegator
 */
class GroupAddressDelegator implements GroupAddressServiceInterface
{
    /**
     * @inheritdoc
     */
    public function attachAddressToGroup(GroupInterface $group, AddressInterface $address) : bool
    {
    }

    /**
     * @inheritdoc
     */
    public function detachAddressFromGroup(GroupInterface $group, AddressInterface $address) : bool
    {
    }

    /**
     * @inheritdoc
     */
    public function fetchAllAddressesForGroup(GroupInterface $group, $where = null, $prototype = null) : DbSelect
    {
    }
}
