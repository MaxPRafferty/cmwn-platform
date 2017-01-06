<?php

namespace GameTest\Service;

use Game\SaveGame;
use Game\Service\SaveGameService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Where;
use Zend\Json\Json;

/**
 * Test SaveGameServiceTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SaveGameServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var SaveGameService
     */
    protected $gameService;

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
        $adapter = \Mockery::mock('\Zend\Db\Adapter\Adapter');
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock('\Zend\Db\TableGateway\TableGateway');
        $this->tableGateway->shouldReceive('getTable')->andReturn('games')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
        $this->tableGateway->shouldReceive('select')
            ->andReturn(new \ArrayIterator([]))
            ->byDefault();
    }

    /**
     * @before
     */
    public function setUpSaveGameService()
    {
        $this->gameService = new SaveGameService($this->tableGateway);
    }

    /**
     * @test
     */
    public function testItShouldSaveGame()
    {
        $saveGame = new SaveGame([
            'game_id' => 'monarch',
            'user_id' => 'manchuck',
            'data'    => ['foo' => 'bar', 'progress' => 100],
            'version' => 'nightly',
        ]);

        $this->tableGateway->shouldReceive('insert')
            ->once()
            ->andReturnUsing(function ($data) use (&$saveGame) {
                $this->assertNotNull($saveGame->getCreated(), 'Date MUST BE added before calling insert');
                $actualData = $saveGame->getArrayCopy();

                // Service MUST convert data to a json string
                $this->assertArrayHasKey('data', $actualData);
                $actualData['data'] = Json::encode($actualData['data']);

                // Service MUST convert date string
                $actualData['created'] = $saveGame->getCreated()->format("Y-m-d H:i:s");

                $this->assertEquals($actualData, $data, '');

                return true;
            });

        $this->assertTrue($this->gameService->saveGame($saveGame), 'Game Service did not return true');
    }

    /**
     * @test
     */
    public function testItShouldRemoveSaveGameUsingStrings()
    {
        $this->tableGateway->shouldReceive('delete')
            ->with(['user_id' => 'manchuck', 'game_id' => 'monarch'])
            ->andReturn(true)
            ->once();

        $this->assertTrue($this->gameService->deleteSaveForUser('manchuck', 'monarch'));
    }

    /**
     * @test
     */
    public function testItShouldFetchSaveForUserWithNoWhere()
    {
        $this->markTestIncomplete('This is not doing anything');
        $date     = new \DateTime();
        $gameData = [
            'game_id' => 'monarch',
            'user_id' => 'manchuck',
            'data'    => ['foo' => 'bar', 'progress' => 100],
            'created' => $date->format("Y-m-d H:i:s"),
            'version' => '8.6.7.5',
        ];

        $result = new ResultSet();
        $result->initialize([$gameData]);

        $this->tableGateway->shouldReceive()
            ->once()
            ->andReturnUsing(function ($where) use (&$result) {
                $this->assertInstanceOf(PredicateInterface::class, $where, 'Where was not created');

                return $result;
            });
    }

    /**
     * @test
     */
    public function testItShouldFetchSaveForUserWithCustomWhereAndPrototype()
    {
        $this->markTestIncomplete('This is doing nothing ');
        $where = new Where();
        $where->addPredicate(new Operator('foo', '=', 'bar'));

        $date     = new \DateTime();
        $gameData = [
            'game_id' => 'monarch',
            'user_id' => 'manchuck',
            'data'    => ['foo' => 'bar', 'progress' => 100],
            'created' => $date->format("Y-m-d H:i:s"),
            'version' => '8.6.7.5',
        ];

        $result = new ResultSet();
        $result->initialize([$gameData]);

        $this->tableGateway->shouldReceive()
            ->once()
            ->andReturnUsing(function ($actualWhere) use (&$result, &$where) {
                $this->assertSame($where, $actualWhere, 'Where was not passed through');

                return $result;
            });
    }

    /**
     * @test
     */
    public function testItShouldRemoveOldSaveBeforeSaving()
    {
        $date     = new \DateTime();
        $gameData = [
            'game_id' => 'monarch',
            'user_id' => 'manchuck',
            'data'    => ['foo' => 'bar', 'progress' => 100],
            'created' => $date->format("Y-m-d H:i:s"),
            'version' => 'v1.2.3',
        ];

        $result = new ResultSet();
        $result->initialize([$gameData]);

        $saveGame = new SaveGame([
            'game_id' => 'monarch',
            'user_id' => 'manchuck',
            'data'    => ['foo' => 'bar', 'progress' => 100],
            'version' => 'v1.2.3',
        ]);

        $this->tableGateway->shouldReceive('insert')
            ->once()
            ->andReturnUsing(function ($data) use (&$saveGame, &$date) {
                $this->assertNotNull($saveGame->getCreated(), 'Date MUST BE added before calling insert');
                $actualData = $saveGame->getArrayCopy();

                // Service MUST convert data to a json string
                $this->assertArrayHasKey('data', $actualData);
                $actualData['data'] = Json::encode($actualData['data']);

                // Service MUST convert date string
                $actualData['created'] = $saveGame->getCreated()->format("Y-m-d H:i:s");

                $this->assertEquals($actualData, $data, '');

                return true;
            });

        $this->assertTrue($this->gameService->saveGame($saveGame), 'Game Service did not return true');
    }
}
