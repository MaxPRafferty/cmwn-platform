<?php

namespace AddressTest\Service;

use Address\Address;
use Address\Service\AddressService;
use Application\Exception\NotFoundException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class AddressServiceTest
 * @package AddressTest\Service
 */
class AddressServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var AddressService
     */
    protected $addressService;

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

        $this->tableGateway = \Mockery::mock(TableGateway::class);
        $this->tableGateway->shouldReceive('getTable')->andReturn('addresses')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->addressService = new AddressService($this->tableGateway);
    }

    /**
     * @test
     */
    public function testItShouldReturnPaginatorAdapterForFetchAllAddressesWithNoWhereAndPrototype()
    {
        $select = new Select(['at' => 'addresses']);
        $select->where(new Where());
        $resultSet = new HydratingResultSet(new ArraySerializable(), new Address([]));
        $expectedSelect = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSet);

        $this->assertEquals($expectedSelect, $this->addressService->fetchAll());
    }

    /**
     * @test
     */
    public function testItShouldReturnPaginatorAdapterForFetchAllAddressesWithWhereAndPrototype()
    {
        $select = new Select(['at' => 'addresses']);
        $select->where(new Where());
        $resultSet = new HydratingResultSet(new ArraySerializable(), new Address([]));
        $expectedSelect = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSet);

        $this->assertEquals($expectedSelect, $this->addressService->fetchAll(null, new Address([])));
    }

    /**
     * @test
     */
    public function testItShouldFetchAddressById()
    {
        $resultSet = new HydratingResultSet();
        $resultSet->initialize([['address_id' => 'foo']]);
        $this->tableGateway->shouldReceive('select')
            ->with(['address_id' => 'foo'])
            ->andReturn($resultSet)
            ->once();

        $address = $this->addressService->fetchAddress('foo');
        $this->assertEquals('foo', $address->getAddressId());
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionIfAddressNotExists()
    {
        $this->setExpectedException(NotFoundException::class);
        $resultSet = new HydratingResultSet();
        $resultSet->initialize([]);
        $this->tableGateway->shouldReceive('select')
            ->with(['address_id' => 'foo'])
            ->andReturn($resultSet)
            ->once();

        $this->addressService->fetchAddress('foo');
    }

    /**
     * @test
     */
    public function testItShouldCreateNewAddress()
    {
        $address = new Address([
            'administrative_area'     => 'ny',
            'sub_administrative_area' => null,
            'locality'                => 'ny',
            'dependent_locality'      => null,
            'postal_code'             => '10036',
            'thoroughfare'            => '21 W 46th St',
            'premise'                 => null,
        ]);

        $this->tableGateway->shouldReceive('insert')
            ->andReturnUsing(function ($address) {
                $this->assertNotNull($address['address_id']);
            })->once();

        $this->assertTrue($this->addressService->createAddress($address));
    }

    /**
     * @test
     */
    public function testItShouldUpdateExistingAddress()
    {
        $address = new Address([
            'address_id'              => 'foo',
            'administrative_area'     => 'ny',
            'sub_administrative_area' => null,
            'locality'                => 'ny',
            'dependent_locality'      => null,
            'postal_code'             => '10036',
            'thoroughfare'            => '21 W 46th St',
            'premise'                 => null,
        ]);

        $this->tableGateway->shouldReceive('update')->once();

        $this->assertTrue($this->addressService->updateAddress($address));
    }

    /**
     * @test
     */
    public function testItShouldDeleteAddress()
    {
        $this->tableGateway->shouldReceive('delete')
            ->with(['address_id' => 'foo'])
            ->once();
        $this->assertTrue($this->addressService->deleteAddress(new Address(['address_id' => 'foo'])));
    }
}
