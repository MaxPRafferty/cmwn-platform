<?php

namespace Group\Service;

use Address\Address;
use Address\AddressInterface;
use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Group\Group;
use Group\GroupInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class GroupAddressService
 * @package Address\Service
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
        try {
            $groupId = $group->getGroupId();
            $addressId = $address->getAddressId();
            $this->tableGateway->insert(['group_id' => $groupId, 'address_id' => $addressId]);
        } catch (\PDOException $exception) {
            if ($exception->getCode()!== 23000) {
                throw $exception;
            }
        }
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
    public function fetchAllAddressesForGroup(
        GroupInterface $group,
        $where = null,
        AddressInterface $prototype = null
    ) : AdapterInterface {
        $groupId = $group->getGroupId();
        $where = $this->createWhere($where);
        $prototype = $prototype ?? new Address([]);
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);

        $where->addPredicate(new Operator('ga.group_id', Operator::OP_EQ, $groupId));

        $select = new Select(['ga' => $this->tableGateway->getTable()]);
        $select->columns([]);
        $select->join(
            ['at' => 'addresses'],
            'at.address_id = ga.address_id',
            '*',
            Select::JOIN_LEFT
        );

        $select->where($where);

        return new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * @param $where
     * @param GroupInterface|null $prototype
     * @return AdapterInterface
     */
    public function fetchAllGroupsInAddress($where = null, GroupInterface $prototype = null) : AdapterInterface
    {
        $where = $this->createWhere($where);
        $select = new Select(['ga' => $this->tableGateway->getTable()]);
        $select->columns([]);
        $select->join(
            ['at' => 'addresses'],
            'ga.address_id = at.address_id'
        );

        $select->columns([]);

        $select->join(
            ['g' => 'groups'],
            'ga.group_id = g.group_id',
            '*',
            Select::JOIN_LEFT
        );

        $select->where($where);
        $prototype = $prototype ?? new Group();
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);
        return new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function fetchAddressForGroup(GroupInterface $group, AddressInterface $address) : AddressInterface
    {
        $addressId = $address->getAddressId();
        $where = new Where([new Operator('ga.address_id', Operator::OP_EQ, $addressId)]);
        $addresses = $this->fetchAllAddressesForGroup($group, $where);

        if ($addresses->count() <= 0) {
            throw new NotFoundException("Address not found");
        }

        return new Address($addresses->getItems(0, 1));
    }

    /**
     * @inheritdoc
     */
    public function fetchAddressesWithGroupsAttached(
        $where = null,
        AddressInterface $prototype = null
    ) : AdapterInterface {
        $where = $this->createWhere($where);
        $select = new Select(['ga' => $this->tableGateway->getTable()]);
        $select->columns([]);
        $select->join(
            ['at' => 'addresses'],
            'at.address_id = ga.address_id',
            '*',
            Select::JOIN_LEFT
        );
        $select->where($where);
        $prototype = $prototype ?? new Address([]);
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);

        return new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function getAlias() : string
    {
        return 'ga';
    }

    /**
     * @inheritdoc
     */
    protected function aliasKeys(array &$where)
    {
        foreach ($where as $field => $value) {
            unset($where[$field]);
            $alias = $this->getAlias();
            if (!($field === 'group_id' || $field === 'address_id')) {
                $alias = 'at';
            }
            $where[$alias . '.' . $field] = $value;
        }
    }
}
