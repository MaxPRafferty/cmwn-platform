<?php

namespace AddressTest\Service;

use Address\Address;
use Group\Service\GroupAddressService;
use Api\V1\Rest\Address\AddressEntity;
use Group\Group;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class GroupAddressServiceTest
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GroupAddressServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var GroupAddressService
     */
    protected $groupAddressService;

    /**
     * @var \Mockery\MockInterface|TableGateway
     */
    protected $tableGateway;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->groupAddressService = new GroupAddressService($this->tableGateway);
    }

    /**
     * @before
     */
    public function setUpGateway()
    {
        /** @var \Mockery\MockInterface|Adapter $adapter */
        $adapter = \Mockery::mock(Adapter::class);
        $adapter->shouldReceive('getPlatform')->byDefault();
        $adapter->shouldReceive('getDriver')->byDefault();

        $this->tableGateway = \Mockery::mock(TableGateway::class);
        $this->tableGateway->shouldReceive('getTable')->andReturn('group_addresses')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @test
     */
    public function testItShouldAttachAddressToGroup()
    {
        $this->tableGateway->shouldReceive('insert')
            ->with(['group_id' => 'foo', 'address_id' => 'bar']);

        $group = new Group();
        $group->setGroupId('foo');
        $this->assertTrue($this->groupAddressService->attachAddressToGroup(
            $group,
            new Address(['address_id' => 'bar'])
        ));
    }

    /**
     * @test
     */
    public function testItShouldNotAttachDuplicateAddressForGroup()
    {
        $this->tableGateway->shouldReceive('insert')
            ->with(['group_id' => 'foo', 'address_id' => 'bar'])
            ->andThrow(new \PDOException("", 23000));

        $group = new Group();
        $group->setGroupId('foo');
        $this->assertTrue($this->groupAddressService->attachAddressToGroup(
            $group,
            new Address(['address_id' => 'bar'])
        ));
    }

    /**
     * @test
     */
    public function testItShouldThrowPDOExceptionIfItIsNotDuplicateEntryException()
    {
        $this->expectException(\PDOException::class);

        $this->tableGateway->shouldReceive('insert')
            ->with(['group_id' => 'foo', 'address_id' => 'bar'])
            ->andThrow(new \PDOException());

        $group = new Group();
        $group->setGroupId('foo');
        $this->assertTrue($this->groupAddressService->attachAddressToGroup(
            $group,
            new Address(['address_id' => 'bar'])
        ));
    }

    /**
     * @test
     */
    public function testItShouldDetachAddressForGroup()
    {
        $this->tableGateway->shouldReceive('delete')
            ->with(['group_id' => 'foo', 'address_id' => 'bar']);

        $group = new Group();
        $group->setGroupId('foo');
        $this->assertTrue($this->groupAddressService->detachAddressFromGroup(
            $group,
            new Address(['address_id' => 'bar'])
        ));
    }

    /**
     * @test
     */
    public function testItShouldReturnPaginatorAdapterForAllAddressesWithNoWhereAndPrototype()
    {
        $where = new Where();
        $where->addPredicate(new Operator('ga.group_id', Operator::OP_EQ, 'foo'));

        $select = new Select(['ga' => $this->tableGateway->getTable()]);
        $select->columns([]);
        $select->join(
            ['at' => 'addresses'],
            'at.address_id = ga.address_id',
            '*',
            Select::JOIN_LEFT
        );

        $select->where($where);

        $adapter = $this->tableGateway->getAdapter();

        $resultSet = new HydratingResultSet(new ArraySerializable(), new Address([]));

        $expectedDbselect = new DbSelect($select, $adapter, $resultSet);

        $group = new Group();
        $group->setGroupId('foo');
        $this->assertEquals(
            $expectedDbselect,
            $this->groupAddressService->fetchAllAddressesForGroup($group)
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnPaginatorAdapterForAllAddressesWithCustomWhereAndPrototype()
    {
        $where = new Where([new Operator('ga.address_id', Operator::OP_EQ, 'bar')]);
        $where->addPredicate(new Operator('ga.group_id', Operator::OP_EQ, 'foo'));

        $select = new Select(['ga' => $this->tableGateway->getTable()]);
        $select->columns([]);
        $select->join(
            ['at' => 'addresses'],
            'at.address_id = ga.address_id',
            '*',
            Select::JOIN_LEFT
        );

        $select->where($where);

        $adapter = $this->tableGateway->getAdapter();

        $resultSet = new HydratingResultSet(new ArraySerializable(), new AddressEntity([]));

        $expectedDbselect = new DbSelect($select, $adapter, $resultSet);

        $group = new Group();
        $group->setGroupId('foo');
        $this->assertEquals(
            $expectedDbselect,
            $this->groupAddressService->fetchAllAddressesForGroup(
                $group,
                new Where([new Operator('ga.address_id', Operator::OP_EQ, 'bar')]),
                new AddressEntity([])
            )
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnPaginatorAdapterForFetchAddressesWithGroupsAttached()
    {
        $where = new Where();

        $select = new Select(['ga' => $this->tableGateway->getTable()]);
        $select->columns([]);
        $select->join(
            ['at' => 'addresses'],
            'at.address_id = ga.address_id',
            '*',
            Select::JOIN_LEFT
        );

        $select->where($where);

        $adapter = $this->tableGateway->getAdapter();

        $resultSet = new HydratingResultSet(new ArraySerializable(), new Address([]));

        $expectedDbselect = new DbSelect($select, $adapter, $resultSet);

        $this->assertEquals(
            $expectedDbselect,
            $this->groupAddressService->fetchAddressesWithGroupsAttached()
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnPaginatorAdapterForFetchAddressesWithGroupsAttachedWithWhereAndPrototype()
    {
        $where = new Where([new Operator('at.postal_code', Operator::OP_EQ, 'bar')]);

        $select = new Select(['ga' => $this->tableGateway->getTable()]);
        $select->columns([]);
        $select->join(
            ['at' => 'addresses'],
            'at.address_id = ga.address_id',
            '*',
            Select::JOIN_LEFT
        );

        $select->where($where);

        $adapter = $this->tableGateway->getAdapter();

        $resultSet = new HydratingResultSet(new ArraySerializable(), new AddressEntity([]));

        $expectedDbselect = new DbSelect($select, $adapter, $resultSet);

        $group = new Group();
        $group->setGroupId('foo');
        $this->assertEquals(
            $expectedDbselect,
            $this->groupAddressService->fetchAddressesWithGroupsAttached(
                new Operator('at.postal_code', '=', 'bar'),
                new AddressEntity([])
            )
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnPaginatorAdapterForFetchAllGroupsInAddress()
    {
        $where = new Where();

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

        $adapter = $this->tableGateway->getAdapter();

        $resultSet = new HydratingResultSet(new ArraySerializable(), new Group());

        $expectedDbselect = new DbSelect($select, $adapter, $resultSet);

        $this->assertEquals(
            $expectedDbselect,
            $this->groupAddressService->fetchAllGroupsInAddress()
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnPaginatorAdapterForFetchAllGroupsInAddressWithWhereAndPrototype()
    {
        $where = new Where([new Operator('at.postal_code', Operator::OP_EQ, 'bar')]);

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

        $adapter = $this->tableGateway->getAdapter();

        $resultSet = new HydratingResultSet(new ArraySerializable(), new Group());

        $expectedDbselect = new DbSelect($select, $adapter, $resultSet);

        $group = new Group();
        $group->setGroupId('foo');
        $this->assertEquals(
            $expectedDbselect,
            $this->groupAddressService->fetchAllGroupsInAddress(
                new Operator('at.postal_code', '=', 'bar'),
                new Group()
            )
        );
    }
}
