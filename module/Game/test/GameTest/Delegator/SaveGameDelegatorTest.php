<?php

namespace GameTest\Delegator;

use Game\Delegator\GameDelegator;
use Game\Delegator\SaveGameDelegator;
use Game\Game;
use Game\SaveGame;
use Game\Service\SaveGameService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use User\PlaceHolder;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;
use Zend\Paginator\Adapter\Iterator;

/**
 * Test for the save game delegator
 */
class SaveGameDelegatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|\Game\Service\SaveGameService
     */
    protected $service;

    /**
     * @var SaveGameDelegator
     */
    protected $delegator;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @var SaveGame
     */
    protected $saveGame;

    /**
     * @var Where
     */
    protected $where;

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->delegator = new SaveGameDelegator($this->service, new EventManager());
        $this->delegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->service = \Mockery::mock(SaveGameService::class);
        $this->where   = new Where();
        $this->service->shouldReceive('createWhere')
            ->andReturn($this->where)
            ->byDefault();
    }

    /**
     * @param Event $event
     */
    public function captureEvents(Event $event)
    {
        $this->calledEvents[] = [
            'name'   => $event->getName(),
            'target' => $event->getTarget(),
            'params' => $event->getParams(),
        ];
    }

    /**
     * @before
     */
    public function setUpSaveGame()
    {
        $this->saveGame = new SaveGame([
            'game_id' => 'monarch',
            'user_id' => 'manchuck',
            'data'    => ['foo' => 'bar', 'progress' => 100],
        ]);
    }

    /**
     * @test
     */
    public function testItShouldCallSaveGame()
    {
        $this->service->shouldReceive('saveGame')
            ->with($this->saveGame)
            ->andReturn(true)
            ->once();

        $this->assertTrue(
            $this->delegator->saveGame($this->saveGame),
            GameDelegator::class . ' did not return true when save game is called'
        );

        $this->assertEquals(
            2,
            count($this->calledEvents),
            GameDelegator::class . ' triggered the incorrect number of events for saveGame'
        );

        $this->assertEquals(
            [
                'name'   => 'save.user.game',
                'target' => $this->service,
                'params' => ['game_data' => $this->saveGame],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' triggered save.user.game incorrectly'
        );

        $this->assertEquals(
            [
                'name'   => 'save.user.game.post',
                'target' => $this->service,
                'params' => ['game_data' => $this->saveGame],
            ],
            $this->calledEvents[1],
            GameDelegator::class . ' triggered save.user.game.post incorrectly'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallSaveGameWhenEventStops()
    {
        $this->service->shouldReceive('saveGame')
            ->never();

        $this->delegator->getEventManager()->attach('save.user.game', function (Event $event) {
            $event->stopPropagation(true);

            return true;
        });

        $this->assertTrue(
            $this->delegator->saveGame($this->saveGame),
            GameDelegator::class . ' did not return result from save.user.game event'
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger the correct amount of events when save.user.game stops'
        );
        $this->assertEquals(
            [
                'name'   => 'save.user.game',
                'target' => $this->service,
                'params' => ['game_data' => $this->saveGame],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger save.user.game event correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallDeleteSaveForUser()
    {
        $user = new PlaceHolder();
        $user->setUserId('manchuck');

        $game = new Game();
        $game->setGameId('monarch');

        $this->service->shouldReceive('deleteSaveForUser')
            ->with($user, $game)
            ->andReturn(true)
            ->once();

        $this->assertTrue(
            $this->delegator->deleteSaveForUser($user, $game),
            GameDelegator::class . ' did not return result from service on deleteSaveForUser'
        );

        $this->assertEquals(
            2,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger correct events for deleteSaveForUser'
        );

        $this->assertEquals(
            [
                'name'   => 'delete.user.save.game',
                'target' => $this->service,
                'params' => ['user' => $user, 'game' => $game],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger delete.user.save.game correctly'
        );

        $this->assertEquals(
            [
                'name'   => 'delete.user.save.game.post',
                'target' => $this->service,
                'params' => ['user' => $user, 'game' => $game],
            ],
            $this->calledEvents[1],
            GameDelegator::class . ' did not trigger delete.user.save.game.post correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallDeleteSaveForUserWhenEventStops()
    {
        $user = new PlaceHolder();
        $user->setUserId('manchuck');

        $game = new Game();
        $game->setGameId('monarch');

        $this->service->shouldReceive('deleteSaveForUser')
            ->never();

        $this->delegator->getEventManager()->attach('delete.user.save.game', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });

        $this->assertFalse(
            $this->delegator->deleteSaveForUser($user, $game),
            GameDelegator::class . ' did not return result from delete.user.save.game'
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            GameDelegator::class . ' did not call the correct number of events for deleteSaveGameForUser'
        );

        $this->assertEquals(
            [
                'name'   => 'delete.user.save.game',
                'target' => $this->service,
                'params' => ['user' => $user, 'game' => $game],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger delete.user.save.game correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchSaveGameForUserWithNoPrototype()
    {
        $user = new PlaceHolder();
        $user->setUserId('manchuck');

        $game = new Game();
        $game->setGameId('monarch');

        $this->service->shouldReceive('fetchSaveGameForUser')
            ->with($user, $game, $this->where, null)
            ->once()
            ->andReturn($this->saveGame);

        $this->assertEquals(
            $this->saveGame,
            $this->delegator->fetchSaveGameForUser($user, $game),
            GameDelegator::class . ' did not return saved game from service'
        );

        $this->assertEquals(
            2,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger the correct events for fetchSaveGameForUser'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.user.save.game',
                'target' => $this->service,
                'params' => [
                    'user'      => $user,
                    'game'      => $game,
                    'prototype' => null,
                    'where'     => $this->where,
                ],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger fetch.user.save.game correctly'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.user.save.game.post',
                'target' => $this->service,
                'params' => [
                    'user'      => $user,
                    'game'      => $game,
                    'prototype' => null,
                    'where'     => $this->where,
                    'game_data' => $this->saveGame,
                ],
            ],
            $this->calledEvents[1],
            GameDelegator::class . ' did not trigger fetch.user.save.game.post correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotFetchSaveGameForUser()
    {
        $user = new PlaceHolder();
        $user->setUserId('manchuck');

        $game = new Game();
        $game->setGameId('monarch');

        $this->service->shouldReceive('fetchSaveGameForUser')
            ->never();

        $this->delegator->getEventManager()->attach('fetch.user.save.game', function (Event $event) {
            $event->stopPropagation(true);

            return $this->saveGame;
        });

        $this->assertSame(
            $this->saveGame,
            $this->delegator->fetchSaveGameForUser($user, $game),
            GameDelegator::class . ' did not return result from fetch.user.save.gaem'
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger the correct events when fetch.user.save.game stops'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.user.save.game',
                'target' => $this->service,
                'params' => [
                    'user'      => $user,
                    'game'      => $game,
                    'prototype' => null,
                    'where'     => $this->where,
                ],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger the fetch.user.save.game correctly for stop'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllSaveGamesForUser()
    {
        $user = new PlaceHolder();
        $user->setUserId('manchuck');

        $results = new Iterator(new \ArrayIterator([]));

        $this->service->shouldReceive('fetchAllSaveGamesForUser')
            ->with($user, $this->where, null)
            ->andReturn($results)
            ->once();

        $this->assertEquals(
            $results,
            $this->delegator->fetchAllSaveGamesForUser($user),
            GameDelegator::class . ' did not return results from fetchAllSaveGameForUser'
        );

        $this->assertEquals(
            2,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger the correct events for fetchAllSaveGameForUser'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.user.saves',
                'target' => $this->service,
                'params' => [
                    'user'      => $user,
                    'prototype' => null,
                    'where'     => $this->where,
                ],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger fetch.user.saves correctly'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.user.saves.post',
                'target' => $this->service,
                'params' => [
                    'user'       => $user,
                    'prototype'  => null,
                    'where'     => $this->where,
                    'user-saves' => $results,
                ],
            ],
            $this->calledEvents[1],
            GameDelegator::class . ' did not trigger fetch.user.saves.post correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotFetchSaveGamesForUserWhenEventStops()
    {
        $user = new PlaceHolder();
        $user->setUserId('manchuck');

        $this->service->shouldReceive('fetchAllSaveGamesForUser')
            ->never();

        $results = new Iterator(new \ArrayIterator([]));

        $this->delegator->getEventManager()->attach('fetch.user.saves', function (Event $event) use (&$results) {
            $event->stopPropagation(true);

            return $results;
        });

        $this->assertSame(
            $results,
            $this->delegator->fetchAllSaveGamesForUser($user),
            GameDelegator::class . ' did not return results from fetch.user.saves'
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger the correct number of events form fetchAllSaveGamesForUser'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.user.saves',
                'target' => $this->service,
                'params' => [
                    'user'      => $user,
                    'prototype' => null,
                    'where'     => $this->where,
                ],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger fetch.user.saves correctly for stopping'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllSaveGameData()
    {
        $results = new Iterator(new \ArrayIterator([]));
        $this->service->shouldReceive('fetchAllSaveGameData')
            ->with($this->where, null)
            ->andReturn($results)
            ->once();

        $this->assertSame(
            $results,
            $this->delegator->fetchAllSaveGameData(),
            GameDelegator::class . ' did not return results from service for fetchAllSaveGameData'
        );

        $this->assertEquals(
            2,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger the correct events fro fetchAllSaveGameData'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.game-data',
                'target' => $this->service,
                'params' => [
                    'prototype' => null,
                    'where'     => $this->where,
                ],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger fetch.game-data correctly'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.game-data.post',
                'target' => $this->service,
                'params' => [
                    'prototype' => null,
                    'where'     => $this->where,
                    'game-data' => $results,
                ],
            ],
            $this->calledEvents[1],
            GameDelegator::class . ' did not trigger fetch.game-data.post correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotFetchAllSaveGameDataWhenEventStops()
    {
        $this->service->shouldReceive('fetchAllSaveGameData')
            ->never();

        $results = new Iterator(new \ArrayIterator([]));

        $this->delegator->getEventManager()->attach('fetch.game-data', function (Event $event) use (&$results) {
            $event->stopPropagation(true);

            return $results;
        });

        $this->assertSame(
            $results,
            $this->delegator->fetchAllSaveGameData(),
            GameDelegator::class . ' did not return results from fetch.game-data'
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger the correct events for fetch.game-data'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.game-data',
                'target' => $this->service,
                'params' => [
                    'prototype' => null,
                    'where'     => $this->where,
                ],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger fetch.game-data for stopping'
        );
    }
}
