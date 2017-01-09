<?php

namespace Address\Service;

use Address\Address;
use Address\AddressInterface;
use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Ramsey\Uuid\Uuid;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class AddressService
 * @package Address\Service
 */
class AddressService implements AddressServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * AddressService constructor.
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @inheritdoc
     */
    public function fetchAddress(string $addressId, $prototype = null) : AddressInterface
    {
        $rowSet = $this->tableGateway->select(['address_id' => $addressId]);

        $row = $rowSet->current();

        if (!$row) {
            throw new NotFoundException("Address not found");
        }

        $prototype = $prototype ?? new Address([]);
        $prototype->exchangeArray((array)$row);

        return $prototype;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, $prototype = null) : DbSelect
    {
        $where = $this->createWhere($where);
        $prototype = $prototype ?? new Address([]);

        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);
        $select = new Select(['at' => $this->tableGateway->getTable()]);

        $select->where($where);

        return new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function createAddress(AddressInterface $address)
    {
        $address->setAddressId(Uuid::uuid1());
        $data = $address->getArrayCopy();
        $this->tableGateway->insert($data);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function updateAddress(AddressInterface $address)
    {
        $this->fetchAddress($address->getAddressId());

        $data = $address->getArrayCopy();

        $this->tableGateway->update($data, ['address_id' => $address->getAddressId()]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteAddress(AddressInterface $address)
    {
        $this->fetchAddress($address->getAddressId());

        $this->tableGateway->delete(['address_id' => $address->getAddressId()]);

        return true;
    }
}
