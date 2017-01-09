<?php


namespace Address\Service;

use Address\Address;
use Address\AddressInterface;
use Application\Utils\ServiceTrait;
use Group\GroupInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class GroupAddressService
 * @package Address\Service
 */
class GroupAddressService implements GroupAddressServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * GroupAddressService constructor.
     * @param TableGateway $tableGateway
     */
    public function __construct($tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @inheritdoc
     */
    public function attachAddressToGroup(GroupInterface $group, AddressInterface $address) : bool
    {
        $groupId = $group->getGroupId();
        $addressId = $address->getAddressId();
        $this->tableGateway->insert(['group_id' => $groupId, 'address_id' => $addressId]);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function detachAddressFromGroup(GroupInterface $group, AddressInterface $address) : bool
    {
        $groupId = $group->getGroupId();
        $addressId = $address->getAddressId();
        $this->tableGateway->delete(['group_id' => $groupId, 'address_id' => $addressId]);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function fetchAllAddressesForGroup(GroupInterface $group, $where = null, $prototype = null) : DbSelect
    {
        $groupId = $group->getGroupId();
        $where = $this->createWhere($where);
        $prototype = $prototype ?? new Address([]);
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);

        $where->addPredicate(new Operator('ga.group_id', Operator::OP_EQ, $groupId));
        $select = new Select(['ga' => $this->tableGateway->getTable()]);
        $select->join(
            ['at' => 'addresses'],
            'at.address_id = ga.address_id',
            'at.*',
            Select::JOIN_LEFT
        );
        $select->where($where);

        return new DbSelect($select, $this->tableGateway->getAdapter(), $resultSet);
    }
}
