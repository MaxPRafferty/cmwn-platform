<?php

namespace AddressTest\Service;

use Address\Address;
use Address\Service\GroupAddressService;
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
 * @package AddressTest\Service
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
    public function setUpGateWay()
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
     * @before
     */
    public function setUpService()
    {
        $this->groupAddressService = new GroupAddressService($this->tableGateway);
    }

    /**
     * @test
     */
    public function testItShouldAttachAddressToGroup()
    {
        $this->tableGateway->shouldReceive('insert')
            ->with(['group_id' => 'foo', 'address_id' => 'bar']);

        $this->assertTrue($this->groupAddressService->attachAddressToGroup(
            new Group(['group_id' => 'foo']),
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

        $this->assertTrue($this->groupAddressService->attachAddressToGroup(
            new Group(['group_id' => 'foo']),
            new Address(['address_id' => 'bar'])
        ));
    }

    /**
     * @test
     */
    public function testItShouldThrowPDOExceptionIfItIsNotDuplicateEntryException()
    {
        $this->setExpectedException(\PDOException::class);

        $this->tableGateway->shouldReceive('insert')
            ->with(['group_id' => 'foo', 'address_id' => 'bar'])
            ->andThrow(new \PDOException());

        $this->assertTrue($this->groupAddressService->attachAddressToGroup(
            new Group(['group_id' => 'foo']),
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

        $this->assertTrue($this->groupAddressService->detachAddressFromGroup(
            new Group(['group_id' => 'foo']),
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

        $this->assertEquals(
            $expectedDbselect,
            $this->groupAddressService->fetchAllAddressesForGroup(new Group(['group_id' => 'foo']))
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

        $this->assertEquals(
            $expectedDbselect,
            $this->groupAddressService->fetchAllAddressesForGroup(
                new Group(['group_id' => 'foo']),
                new Where([new Operator('ga.address_id', Operator::OP_EQ, 'bar')]),
                new AddressEntity([])
            )
        );
    }
}
