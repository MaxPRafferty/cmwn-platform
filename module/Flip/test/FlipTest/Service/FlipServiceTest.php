<?php

namespace FlipTest\Service;

use Application\Exception\NotFoundException;
use Flip\Flip;
use Flip\Service\FlipService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
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
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FlipServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var FlipService
     */
    protected $flipService;

    /**
     * @var \Mockery\MockInterface|TableGateway
     */
    protected $tableGateway;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->flipService = new FlipService($this->tableGateway);
    }

    /**
     * @before
     */
    public function setUpGateWay()
    {
        /** @var \Mockery\MockInterface|Adapter $adapter */
        $adapter = \Mockery::mock(Adapter::class);
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock(TableGateway::class);
        $this->tableGateway->shouldReceive('getTable')->andReturn('flips')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
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
        $expectedAdapter = new DbSelect(
            $expectedSelect,
            $this->tableGateway->getAdapter(),
            $expectedResultSet
        );

        $this->assertEquals(
            $expectedAdapter,
            $this->flipService->fetchAll(),
            FlipService::class . ' did not return the correct paginator adapter with default prototype'
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

        $expectedAdapter = new DbSelect(
            $expectedSelect,
            $this->tableGateway->getAdapter(),
            $expectedResultSet
        );

        $this->assertEquals(
            $expectedAdapter,
            $this->flipService->fetchAll($where, $prototype),
            FlipService::class . ' did not return the correct paginator adapter with custom prototype'
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
            FlipService::class . ' did not return back a flip'
        );

        $this->assertEquals(
            new Flip($flipData),
            $actualFlip,
            FlipService::class . ' did failed to hydrate flip'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenFlipNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Flip not Found');

        $result = new ResultSet();
        $result->initialize([]);
        $this->tableGateway->shouldReceive('select')
            ->andReturn($result);

        $this->flipService->fetchFlipById('foo-bar');
    }
}
