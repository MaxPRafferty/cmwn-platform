<?php

namespace GameTest\Service;

use Game\Game;
use Game\SaveGame;
use Game\SaveGameInterface;
use Game\Service\GameService;
use Game\Service\SaveGameService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use User\Child;
use User\PlaceHolder;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\TableGateway\TableGateway;
use Zend\Json\Json;

/**
 * Tests the Save Game Service
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
    public function setUpSaveGameService()
    {
        $this->gameService = new SaveGameService($this->tableGateway);
    }

    /**
     * @before
     */
    public function setUpGateWay()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter */
        $adapter = \Mockery::mock(Adapter::class);
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock(TableGateway::class);
        $this->tableGateway->shouldReceive('getTable')->andReturn('games')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
        $this->tableGateway->shouldReceive('select')
            ->andReturn(new \ArrayIterator([]))
            ->byDefault();
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
            ->withArgs(function ($data) use (&$saveGame) {
                $actualData            = $saveGame->getArrayCopy();
                $actualData['data']    = Json::encode($actualData['data']);
                $actualData['created'] = $saveGame->getCreated()->format("Y-m-d H:i:s");

                return $actualData == $data;
            })
            ->andReturn(true);

        $this->assertTrue(
            $this->gameService->saveGame($saveGame),
            GameService::class . ' did not return true on saveGame'
        );
    }

    /**
     * @test
     */
    public function testItShouldUpsertGame()
    {
        $saveGame = new SaveGame([
            'game_id' => 'monarch',
            'user_id' => 'manchuck',
            'data'    => ['foo' => 'bar', 'progress' => 100],
            'version' => 'nightly',
        ]);

        $this->tableGateway->shouldReceive('insert')
            ->once()
            ->andThrow(new InvalidQueryException(null, null, new \PDOException(null, '23000')));

        $this->tableGateway->shouldReceive('update')
            ->once()
            ->withArgs(function ($actualData, $where) use (&$saveGame) {
                $expectedData = [
                    'data'    => Json::encode($saveGame->getData()),
                    'created' => $saveGame->getCreated()->format("Y-m-d H:i:s"),
                    'version' => 'nightly',
                ];

                $this->assertEquals(
                    ['user_id' => 'manchuck', 'game_id' => 'monarch'],
                    $where,
                    GameService::class . ' is building the wrong where when updateing'
                );

                $this->assertEquals(
                    $expectedData,
                    $actualData,
                    GameService::class . ' is not going to update the save game correctly'
                );

                return true;
            })
            ->andReturn(true);

        $this->assertTrue(
            $this->gameService->saveGame($saveGame),
            GameService::class . ' did not return true on saveGame'
        );
    }

    /**
     * @test
     */
    public function testItShouldRemoveSaveGame()
    {
        $game = new Game();
        $game->setGameId('monarch');

        $user = new Child();
        $user->setUserId('manchuck');
        $this->tableGateway->shouldReceive('delete')
            ->with(['user_id' => 'manchuck', 'game_id' => 'monarch'])
            ->andReturn(true)
            ->once();

        $this->assertTrue(
            $this->gameService->deleteSaveForUser($user, $game),
            GameService::class . ' did not return true on successful delete with user and game'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchSaveForUserWithStringsAndNoPrototype()
    {
        $user = new PlaceHolder();
        $user->setUserId('manchuck');

        $game = new Game();
        $game->setGameId('monarch');

        $date     = new \DateTime();
        $gameData = [
            'game_id' => 'monarch',
            'user_id' => 'manchuck',
            'data'    => ['foo' => 'bar', 'progress' => 100],
            'created' => $date->format("Y-m-d H:i:s"),
            'version' => '8.6.7.5',
        ];

        $expectedResult = new SaveGame($gameData);
        $result         = new ResultSet();
        $result->initialize([$gameData]);

        $this->tableGateway->shouldReceive('select')
            ->once()
            ->withArgs(function ($actualWhere) {
                $expectedWhere = $this->gameService->createWhere([]);
                $expectedWhere->addPredicate(new Operator('user_id', '=', 'manchuck'));
                $expectedWhere->addPredicate(new Operator('game_id', '=', 'monarch'));

                return $expectedWhere == $actualWhere;
            })
            ->andReturn($result);

        $this->assertEquals(
            $expectedResult,
            $this->gameService->fetchSaveGameForUser($user, $game),
            GameService::class . ' did not return back a default save game when fetching by strings'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchSaveForUserWithUserGameAndPrototype()
    {
        /** @var \Mockery\MockInterface|SaveGameInterface $prototype */
        $prototype = \Mockery::mock(SaveGameInterface::class);

        $date     = new \DateTime();
        $gameData = [
            'game_id' => 'monarch',
            'user_id' => 'manchuck',
            'data'    => ['foo' => 'bar', 'progress' => 100],
            'created' => $date->format("Y-m-d H:i:s"),
            'version' => '8.6.7.5',
        ];

        $user = new Child();
        $user->setUserId('manchuck');

        $game = new Game();
        $game->setGameId('monarch');

        $prototype->shouldReceive('exchangeArray')
            ->with($gameData);

        $result = new ResultSet();
        $result->initialize([$gameData]);

        $this->tableGateway->shouldReceive('select')
            ->once()
            ->withArgs(function ($actualWhere) {
                $expectedWhere = $this->gameService->createWhere([]);
                $expectedWhere->addPredicate(new Operator('user_id', '=', 'manchuck'));
                $expectedWhere->addPredicate(new Operator('game_id', '=', 'monarch'));

                return $expectedWhere == $actualWhere;
            })
            ->andReturn($result);

        $this->assertEquals(
            $prototype,
            $this->gameService->fetchSaveGameForUser($user, $game, null, $prototype),
            GameService::class . ' did not return back prototype when fetching a saveGame for a user'
        );
    }
}
