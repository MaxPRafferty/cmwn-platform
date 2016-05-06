<?php

namespace FlipTest\Service;

use Application\Exception\NotFoundException;
use Flip\Flip;
use Flip\Service\FlipService;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Test FlipServiceTest
 *
 * @group Flip
 * @group Service
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
        $expectedSelect    = new Select(['f' => 'flips']);
        $expectedSelect->where(new Where());
        $expectedSelect->order(['f.title']);
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
        $where     = new Where();
        $where->addPredicate(new Operator('foo', '=', 'bar'));

        $expectedResultSet = new HydratingResultSet(new ArraySerializable(), $prototype);
        $expectedSelect    = new Select(['f' => 'flips']);
        $expectedSelect->where($where);
        $expectedSelect->order(['f.title']);

        $expectedAdapter   = new DbSelect(
            $expectedSelect,
            $this->tableGateway->getAdapter(),
            $expectedResultSet
        );

        $this->assertEquals(
            $expectedAdapter,
            $this->flipService->fetchAll($where, $prototype),
            'Flip Service did not return correct adapter'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchFlipById()
    {
        $flipData = [
            'flip_id'     => 'foo-bar',
            'title'       => 'Manchuck Flip',
            'description' => 'The Best Flip to earn',
        ];

        $result = new ResultSet();
        $result->initialize([$flipData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['flip_id' => $flipData['flip_id']])
            ->andReturn($result);

        $actualFlip = $this->flipService->fetchFlipById($flipData['flip_id']);
        $this->assertInstanceOf(
            Flip::class,
            $actualFlip,
            'Flip service did not return back a flip'
        );

        $this->assertEquals(new Flip($flipData), $actualFlip, 'Flip returned was not correctly');
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenFlipNotFound()
    {
        $this->setExpectedException(
            NotFoundException::class,
            'Flip not Found'
        );

        $result = new ResultSet();
        $result->initialize([]);
        $this->tableGateway->shouldReceive('select')
            ->andReturn($result);

        $this->flipService->fetchFlipById('foo-bar');
    }
}
