<?php

namespace FlipTest\Service;

use Flip\Flip;
use Flip\Service\FlipService;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Test FlipServiceTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FlipServiceTest extends TestCase
{
    /**
     * @var FlipService
     */
    protected $flipService;

    /**
     * @var \Mockery\MockInterface|\Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * @before
     */
    public function setUpGateWay()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter */
        $adapter = \Mockery::mock(\Zend\Db\Adapter\Adapter::class);
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock(\Zend\Db\TableGateway\TableGateway::class);
        $this->tableGateway->shouldReceive('getTable')->andReturn('flips')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->flipService = new FlipService($this->tableGateway);
    }

    /**
     * @test
     */
    public function testItShouldReturnPaginatorAdapterForAllFlipsWithNoWhereAndPrototype()
    {
        $expectedResultSet = new HydratingResultSet(new ArraySerializable(), new Flip());
        $expectedSelect    = new Select('flips');
        $expectedSelect->where(new Where());

        $expectedAdapter   = new DbSelect(
            $expectedSelect,
            $this->tableGateway->getAdapter(),
            $expectedResultSet
        );

        $this->assertEquals(
            $expectedAdapter,
            $this->flipService->fetchAll(),
            'Flip Service did not return correct adapter'
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnPaginatorAdapterForAllFlipsWithCustomWhereAndPrototype()
    {
        /** @var \Mockery\MockInterface|\Flip\FlipInterface $prototype */
        $prototype = \Mockery::mock('\Flip\FlipInterface');
        

        $expectedResultSet = new HydratingResultSet(new ArraySerializable(), $prototype);
        $expectedSelect    = new Select('flips');
        $expectedSelect->where(new Where());

        $expectedAdapter   = new DbSelect(
            $expectedSelect,
            $this->tableGateway->getAdapter(),
            $expectedResultSet
        );

        $this->assertEquals(
            $expectedAdapter,
            $this->flipService->fetchAll(),
            'Flip Service did not return correct adapter'
        );
    }
}
