<?php

namespace GameTest\Delegator;

use Application\Exception\NotFoundException;
use Game\Delegator\GameDelegator;
use Game\Game;
use Game\GameInterface;
use Game\Service\GameService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;
use Zend\Paginator\Adapter\Iterator;

/**
 * Unit tests for game delegator
 */
class GameDelegatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface | \Game\Service\GameService
     */
    protected $gameService;

    /**
     * @var GameDelegator
     */
    protected $gameDelegator;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->calledEvents  = [];
        $this->gameDelegator = new GameDelegator($this->gameService, new EventManager());
        $this->gameDelegator->getEventManager()->clearListeners('fetch.all.games');
        $this->gameDelegator->getEventManager()->clearListeners('fetch.game.post');
        $this->gameDelegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->gameService = \Mockery::mock(GameService::class);
        $this->gameService->shouldReceive('createWhere')
            ->andReturns();
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
     * @test
     */
    public function testItShouldCallFetchAll()
    {
        $result = new Iterator(new \ArrayIterator([]));

        $where = $this->gameDelegator->createWhere(null);
        $this->gameService
            ->shouldReceive('fetchAll')
            ->with($where, null, false)
            ->andReturn($result);

        $this->assertSame(
            $result,
            $this->gameDelegator->fetchAll($where),
            GameDelegator::class . ' did not return the result from the game service on fetch all'
        );

        $this->assertEquals(
            2,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger the expected number of events for fetchAll'
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.all.games',
                'target' => $this->gameService,
                'params' => [
                    'where'        => $where,
                    'prototype'    => null,
                    'show_deleted' => false,
                ],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger fetch.all.games with the correct parameters'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.games.post',
                'target' => $this->gameService,
                'params' => [
                    'where'        => $where,
                    'prototype'    => null,
                    'show_deleted' => false,
                    'results'      => $result,
                ],
            ],
            $this->calledEvents[1],
            GameDelegator::class . ' did not trigger fetch.all.games.post with the correct parameters'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchAllWithPrototype()
    {
        /** @var \Mockery\MockInterface|GameInterface $prototype */
        $prototype = \Mockery::mock(GameInterface::class);

        $result = new Iterator(new \ArrayIterator([]));

        $where = $this->gameDelegator->createWhere(null);
        $this->gameService
            ->shouldReceive('fetchAll')
            ->with($where, $prototype, false)
            ->once()
            ->andReturn($result);

        $this->assertSame(
            $result,
            $this->gameDelegator->fetchAll($where, $prototype),
            GameDelegator::class . ' did not return the result from the game service on fetch all'
        );

        $this->assertEquals(
            2,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger the expected number of events for fetchAll'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.games',
                'target' => $this->gameService,
                'params' => [
                    'where'        => $where,
                    'prototype'    => $prototype,
                    'show_deleted' => false,
                ],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger fetch.all.games with the correct parameters'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.games.post',
                'target' => $this->gameService,
                'params' => [
                    'where'        => $where,
                    'prototype'    => $prototype,
                    'show_deleted' => false,
                    'results'      => $result,
                ],
            ],
            $this->calledEvents[1],
            GameDelegator::class . ' did not trigger fetch.all.games.post with the correct parameters'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchAllWhenEvenStops()
    {
        $result = new Iterator(new \ArrayIterator([]));
        $this->gameService
            ->shouldReceive('fetchAll')
            ->never();

        $this->gameDelegator->getEventManager()
            ->attach('fetch.all.games', function (Event $event) use (&$result) {
                $event->stopPropagation(true);

                return $result;
            });

        $this->assertEquals(
            $result,
            $this->gameDelegator->fetchAll(),
            GameDelegator::class . ' did not return the result from the listener from fetchAll'
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger the correct number of events when a listener prevents propagation'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.games',
                'target' => $this->gameService,
                'params' => [
                    'where'        => $this->gameDelegator->createWhere(null),
                    'prototype'    => null,
                    'show_deleted' => false,
                ],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger fetch.all.games correctly for fetch.all.games'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchGame()
    {
        $result = new Game();

        $this->gameService
            ->shouldReceive('fetchGame')
            ->andReturn($result);

        $this->gameDelegator->fetchGame('qwerty');
        $this->assertEquals(
            2,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger the correct events for fetch.game'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.game',
                'target' => $this->gameService,
                'params' => ['game_id' => 'qwerty', 'prototype' => null],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not set correct event for fetch.game'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.game.post',
                'target' => $this->gameService,
                'params' => ['game_id' => 'qwerty', 'game' => $result, 'prototype' => null],
            ],
            $this->calledEvents[1],
            GameDelegator::class . ' did not set correct event for fetch.game.post'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchGameWithPrototype()
    {
        $prototype = new Game();

        $this->gameService
            ->shouldReceive('fetchGame')
            ->andReturn($prototype);

        $this->assertSame(
            $this->gameDelegator->fetchGame('qwerty', $prototype),
            $prototype,
            GameDelegator::class . ' did not return the same prototype on fetch game'
        );

        $this->assertEquals(
            2,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger the correct events for fetch.game with a prototype'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.game',
                'target' => $this->gameService,
                'params' => ['game_id' => 'qwerty', 'prototype' => $prototype],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not set correct event for fetch.game with a prototype'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.game.post',
                'target' => $this->gameService,
                'params' => ['game_id' => 'qwerty', 'game' => $prototype, 'prototype' => $prototype],
            ],
            $this->calledEvents[1],
            GameDelegator::class . ' did not set correct event for fetch.game.post with a prototype'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallPostEventWhenGameNotFound()
    {
        $exception = new NotFoundException('No game Found');
        $this->gameService->shouldReceive('fetchGame')
            ->andThrow($exception)->once();

        try {
            $this->gameDelegator->fetchGame('qwerty');
            $this->fail(GameDelegator::class . ' did not re-throw the NotFound Exception');
        } catch (NotFoundException $nf) {
            //noop
        }

        $this->assertEquals(
            2,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger the correct number of events when game not found'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.game',
                'target' => $this->gameService,
                'params' => ['game_id' => 'qwerty', 'prototype' => null],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger the correct events for fetch.game when game not found'
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.game.error',
                'target' => $this->gameService,
                'params' => ['game_id' => 'qwerty', 'prototype' => null, 'error' => $exception],
            ],
            $this->calledEvents[1],
            GameDelegator::class . ' did not trigger the fetch.game.error event correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchGameWhenEventStops()
    {
        $game = new Game();
        $this->gameService
            ->shouldReceive('fetchGame')
            ->never();

        $this->gameDelegator->getEventManager()->attach('fetch.game', function (Event $event) use (&$game) {
            $event->stopPropagation(true);

            return $game;
        });

        $this->assertEquals(
            $game,
            $this->gameDelegator->fetchGame('qwerty'),
            GameDelegator::class . ' did not return back the game when the event fetch.game is stopped'
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            GameDelegator::class . ' triggered an incorrect number of events when fetch.game stops'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.game',
                'target' => $this->gameService,
                'params' => ['game_id' => 'qwerty', 'prototype' => null],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger fetch.game correct on stopping event'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallCreateGame()
    {
        $game = new Game();
        $this->gameService->shouldReceive('createGame')
            ->with($game)
            ->once()
            ->andReturn(true);

        $this->assertTrue(
            $this->gameDelegator->createGame($game),
            GameDelegator::class . ' did not return the result from the service with create game'
        );

        $this->assertEquals(
            2,
            count($this->calledEvents),
            GameDelegator::class . ' did triggered the incorrect number of events for create game'
        );

        $this->assertEquals(
            [
                'name'   => 'create.game',
                'target' => $this->gameService,
                'params' => ['game' => $game],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger create.game correctly'
        );
        $this->assertEquals(
            [
                'name'   => 'create.game.post',
                'target' => $this->gameService,
                'params' => ['game' => $game],
            ],
            $this->calledEvents[1],
            GameDelegator::class . ' did not trigger create.game.post correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallCreateGameWhenEventStops()
    {
        $game = new Game();
        $this->gameService
            ->shouldReceive('createGame')
            ->never();

        $this->gameDelegator->getEventManager()->attach('create.game', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });

        $this->assertFalse(
            $this->gameDelegator->createGame($game),
            GameDelegator::class . ' did not return the result from the event on createGame'
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger the correct events when create.game stops'
        );

        $this->assertEquals(
            [
                'name'   => 'create.game',
                'target' => $this->gameService,
                'params' => ['game' => $game],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger create.game correctly stops'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallSaveGame()
    {
        $game = new Game();
        $this->gameService->shouldReceive('saveGame')
            ->with($game)
            ->once()
            ->andReturn(true);

        $this->assertTrue(
            $this->gameDelegator->saveGame($game),
            GameDelegator::class . ' did not return the result from saveGame'
        );

        $this->assertEquals(
            2,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger the correct number of events when updating a game'
        );

        $this->assertEquals(
            [
                'name'   => 'update.game',
                'target' => $this->gameService,
                'params' => ['game' => $game],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger update.game correctly'
        );
        $this->assertEquals(
            [
                'name'   => 'update.game.post',
                'target' => $this->gameService,
                'params' => ['game' => $game],
            ],
            $this->calledEvents[1],
            GameDelegator::class . ' did not trigger update.game.post correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallSaveGameWhenEventStops()
    {
        $game = new Game();
        $this->gameService->shouldReceive('saveGame')
            ->never();

        $this->gameDelegator->getEventManager()->attach('update.game', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });

        $this->assertFalse(
            $this->gameDelegator->saveGame($game),
            GameDelegator::class . ' did not return the result from the update.game event'
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            GameDelegator::class . ' did called the incorrect number of events when update.game stops'
        );

        $this->assertEquals(
            [
                'name'   => 'update.game',
                'target' => $this->gameService,
                'params' => ['game' => $game],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not call update.game correctly when the event stops'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallDeleteGame()
    {
        $game = new Game();
        $this->gameService->shouldReceive('deleteGame')
            ->with($game)
            ->andReturn(true);

        $this->assertTrue(
            $this->gameDelegator->deleteGame($game),
            GameDelegator::class . ' did not return the result from the service for delete game'
        );

        $this->assertEquals(
            2,
            count($this->calledEvents),
            GameDelegator::class . ' did not trigger the correct number of events when deleting a game'
        );

        $this->assertEquals(
            [
                'name'   => 'delete.game',
                'target' => $this->gameService,
                'params' => ['game' => $game, 'soft' => true],
            ],
            $this->calledEvents[0],
            GameDelegator::class . ' did not trigger delete.game correctly'
        );

        $this->assertEquals(
            [
                'name'   => 'delete.game.post',
                'target' => $this->gameService,
                'params' => ['game' => $game, 'soft' => true],
            ],
            $this->calledEvents[1],
            GameDelegator::class . ' did not trigger delete.game.post correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallDeleteGameWhenEventStops()
    {
        $game = new Game();
        $this->gameService
            ->shouldReceive('deleteGame')
            ->never();

        $this->gameDelegator->getEventManager()->attach('delete.game', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });

        $this->assertFalse(
            $this->gameDelegator->deleteGame($game, false),
            GameDelegator::class . ' did not return the value from the delete.game event'
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            GameDelegator::class . ' did called the incorrect number of events when deleting a game'
        );

        $this->assertEquals(
            [
                'name'   => 'delete.game',
                'target' => $this->gameService,
                'params' => ['game' => $game, 'soft' => false],
            ],
            $this->calledEvents[0]
        );
    }
}
