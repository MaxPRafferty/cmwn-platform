<?php

namespace FlipTest\Service;

use Application\Exception\NotFoundException;
use Flip\Flip;
use Flip\FlipInterface;
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
use Zend\Json\Json;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Test FlipServiceTest
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
     * @var Flip
     */
    protected $flip;

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
    public function setUpDefaultTestFlip()
    {
        $this->flip = new Flip([
            'flip_id'     => 'foo-bar',
            'title'       => 'Manchuck Flip',
            'description' => 'The Best Flip to earn',
            'urls'        => [
                Flip::IMAGE_COIN     => 'https://media.changemyworldnow.com/f/34897dva89s7490890.png',
                Flip::IMAGE_UNEARNED => 'https://media.changemyworldnow.com/f/34897dva89s7490890.png',
                Flip::IMAGE_EARNED   => 'https://media.changemyworldnow.com/f/34897dva89s7490890.png',
                Flip::IMAGE_STATIC   => 'https://media.changemyworldnow.com/f/34897dva89s7490890.png',
                Flip::IMAGE_DEFAULT  => 'https://media.changemyworldnow.com/f/34897dva89s7490890.png',
            ],
        ]);
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
    public function testItShouldFetchAllFlips()
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
    public function testItShouldFetchAllFlipsWithCustomWhereAndPrototype()
    {
        /** @var \Mockery\MockInterface|\Flip\FlipInterface $prototype */
        $prototype = \Mockery::mock(FlipInterface::class);
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
    public function testItShouldFetchFlip()
    {
        $flipData = [
            'flip_id'     => $this->flip->getFlipId(),
            'title'       => $this->flip->getTitle(),
            'description' => $this->flip->getDescription(),
            'uris'        => Json::encode($this->flip->getUris()),
        ];

        $result = new ResultSet();
        $result->initialize([$flipData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['flip_id' => $flipData['flip_id']])
            ->andReturn($result);

        $actualFlip = $this->flipService->fetchFlipById($flipData['flip_id']);

        $this->assertEquals(
            new Flip($flipData),
            $actualFlip,
            FlipService::class . ' did failed to hydrate flip'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchFlipWithPrototype()
    {
        /** @var \Mockery\MockInterface|\Flip\FlipInterface $prototype */
        $prototype = \Mockery::mock(FlipInterface::class);

        $flipData = [
            'flip_id'     => $this->flip->getFlipId(),
            'title'       => $this->flip->getTitle(),
            'description' => $this->flip->getDescription(),
            'uris'        => Json::encode($this->flip->getUris()),
        ];

        $result = new ResultSet();
        $result->initialize([$flipData]);
        $this->tableGateway->shouldReceive('select')
            ->once()
            ->with(['flip_id' => $flipData['flip_id']])
            ->andReturn($result);

        $prototype->shouldReceive('exchangeArray')
            ->once()
            ->with($flipData);

        $actualFlip = $this->flipService->fetchFlipById($flipData['flip_id'], $prototype);

        $this->assertSame(
            $prototype,
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
            ->once()
            ->andReturn($result);

        $this->flipService->fetchFlipById('foo-bar');
    }

    /**
     * @test
     */
    public function testItShouldCreateFlip()
    {
        $this->tableGateway->shouldReceive('insert')
            ->once()
            ->withArgs(function ($actualData) {
                $expectedData = [
                    'flip_id'     => $this->flip->getFlipId(),
                    'title'       => $this->flip->getTitle(),
                    'description' => $this->flip->getDescription(),
                    'uris'        => Json::encode($this->flip->getUris()),
                ];

                $this->assertEquals(
                    $expectedData,
                    $actualData,
                    FlipService::class . ' is not inserting a flip correctly'
                );

                return true;
            })
            ->andReturn(1);

        $this->assertTrue(
            $this->flipService->createFlip($this->flip),
            FlipService::class . ' did not return true when a flip is created'
        );
    }

    /**
     * @test
     */
    public function testItShouldUpdateFlip()
    {
        $this->tableGateway->shouldReceive('update')
            ->once()
            ->withArgs(function ($actualWhere, $actualData) {
                $expectedWhere = [
                    'flip_id' => $this->flip->getFlipId(),
                ];
                $expectedData  = [
                    'title'       => $this->flip->getTitle(),
                    'description' => $this->flip->getDescription(),
                    'uris'        => Json::encode($this->flip->getUris()),
                ];

                $this->assertEquals(
                    $expectedData,
                    $actualData,
                    FlipService::class . ' is not updating the flip correctly'
                );

                $this->assertEquals(
                    $expectedWhere,
                    $actualWhere,
                    FlipService::class . ' is not updating the correct flip'
                );

                return true;
            })
            ->andReturn(1);

        $this->assertTrue(
            $this->flipService->updateFlip($this->flip),
            FlipService::class . ' did not return true when a flip is updated'
        );
    }

    /**
     * @test
     */
    public function testItShouldDeleteFlip()
    {
        $this->tableGateway->shouldReceive('delete')
            ->once()
            ->with(['flip_id' => 'foo-bar'])
            ->andReturn(1);

        $this->assertTrue(
            $this->flipService->deleteFlip($this->flip),
            FlipService::class . ' did not return true when a flip is deleted'
        );
    }
}
