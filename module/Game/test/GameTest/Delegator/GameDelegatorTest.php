<?php

namespace GameTest\Delegator;

use Application\Exception\NotFoundException;
use Game\Delegator\GameDelegator;
use Game\Game;
use Game\Service\GameService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;

/**
 * Class GameDelegatorTest
 *
 * @package GameTest\Delegator
 * @SuppressWarnings(PHPMD)
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
    public function setUpService()
    {
        $this->gameService = \Mockery::mock(GameService::class);
    }

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $events = new EventManager();
        $this->gameDelegator = new GameDelegator($this->gameService, $events);
        $this->gameDelegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
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
        $result = new \ArrayIterator([]);

        $this->gameService
            ->shouldReceive('fetchAll')
            ->andReturn($result);
        $this->gameDelegator->fetchAll();
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.games',
                'target' => $this->gameService,
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.all.games.post',
                'target' => $this->gameService,
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchAllWhenEvenStops()
    {
        $this->gameService
            ->shouldReceive('fetchAll')
            ->never();
        $this->gameDelegator->getEventManager()->attach('fetch.all.games', function (Event $event) {
            $event->stopPropagation(true);

            return 'foo';
        });

        $this->assertEquals('foo', $this->gameDelegator->fetchAll());

        $this->assertEquals(1, count($this->calledEvents));

        $this->assertEquals(
            [
                'name'   => 'fetch.all.games',
                'target' => $this->gameService,
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null],
            ],
            $this->calledEvents[0]
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
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.game',
                'target' => $this->gameService,
                'params' => ['game_id' => 'qwerty'],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.game.post',
                'target' => $this->gameService,
                'params' => ['game_id' => 'qwerty', 'game' => $result],
            ],
            $this->calledEvents[1]
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
        } catch (NotFoundException $nf) {
            //noop
        }

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.game',
                'target' => $this->gameService,
                'params' => ['game_id' => 'qwerty'],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchGameWhenEventStops()
    {
        $this->gameService
            ->shouldReceive('fetchGame')
            ->never();
        $this->gameDelegator->getEventManager()->attach('fetch.game', function (Event $event) {
            $event->stopPropagation(true);

            return 'foo';
        });

        $this->assertEquals('foo', $this->gameDelegator->fetchGame('qwerty'));

        $this->assertEquals(1, count($this->calledEvents));

        $this->assertEquals(
            [
                'name'   => 'fetch.game',
                'target' => $this->gameService,
                'params' => ['game_id' => 'qwerty'],
            ],
            $this->calledEvents[0]
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
            ->andReturn(true);
        $this->assertTrue($this->gameDelegator->createGame($game));
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'create.game',
                'target' => $this->gameService,
                'params' => ['game' => $game],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'create.game.post',
                'target' => $this->gameService,
                'params' => ['game' => $game],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallCreateGameWhenEventStops()
    {
        $game = new Game();
        $this->gameService->shouldReceive('createGame')
            ->never();
        $this->gameDelegator->getEventManager()->attach('create.game', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });
        $this->assertFalse($this->gameDelegator->createGame($game));
        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'create.game',
                'target' => $this->gameService,
                'params' => ['game' => $game],
            ],
            $this->calledEvents[0]
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
            ->andReturn(true);
        $this->assertTrue($this->gameDelegator->saveGame($game));
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'update.game',
                'target' => $this->gameService,
                'params' => ['game' => $game],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'update.game.post',
                'target' => $this->gameService,
                'params' => ['game' => $game],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallSaveGameWhenEventStops()
    {
        $game = new Game();
        $this->gameService->shouldReceive('saveGame')
            ->with($game);
        $this->gameDelegator->getEventManager()->attach('update.game', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });
        $this->assertFalse($this->gameDelegator->saveGame($game));
        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'update.game',
                'target' => $this->gameService,
                'params' => ['game' => $game],
            ],
            $this->calledEvents[0]
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
        $this->assertTrue($this->gameDelegator->deleteGame($game));
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.game',
                'target' => $this->gameService,
                'params' => ['game' => $game],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'delete.game.post',
                'target' => $this->gameService,
                'params' => ['game' => $game],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallDeleteGameWhenEventStops()
    {
        $game = new Game();
        $this->gameService->shouldReceive('deleteGame')
            ->with($game);
        $this->gameDelegator->getEventManager()->attach('delete.game', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });
        $this->assertFalse($this->gameDelegator->deleteGame($game));
        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.game',
                'target' => $this->gameService,
                'params' => ['game' => $game],
            ],
            $this->calledEvents[0]
        );
    }
}
