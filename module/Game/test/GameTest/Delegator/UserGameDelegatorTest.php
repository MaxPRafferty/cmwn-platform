<?php

namespace GameTest\Delegator;

use Application\Exception\NotFoundException;
use Game\Game;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Game\Service\UserGameService;
use Game\Delegator\UserGameServiceDelegator;
use User\Child;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;
use Zend\Paginator\Adapter\Iterator;

/**
 * Unit tests for UserGameDelegator
 */
class UserGameDelegatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|UserGameService
     */
    protected $userGameService;

    /**
     * @var UserGameServiceDelegator
     */
    protected $delegator;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->userGameService = \Mockery::mock(UserGameService::class);

        $events             = new EventManager();
        $this->calledEvents = [];
        $this->delegator    = new UserGameServiceDelegator($this->userGameService, $events);
        $events->attach('*', [$this, 'captureEvents'], PHP_INT_MAX);
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
    public function testItShouldCallAttachGameToUser()
    {
        $game = new Game([
            'game_id' => 'foo',
        ]);

        $user = new Child(['user_id' => 'bar']);

        $this->userGameService->shouldReceive('attachGameToUser')
            ->andReturn(true)->once();

        $this->delegator->attachGameToUser($user, $game);

        $this->assertEquals(
            2,
            count($this->calledEvents),
            UserGameServiceDelegator::class . ' did not trigger the correct number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'attach.user.game',
                'target' => $this->userGameService,
                'params' => ['user' => $user, 'game' => $game],
            ],
            $this->calledEvents[0],
            UserGameServiceDelegator::class . ' did not trigger the event correctly'
        );

        $this->assertEquals(
            [
                'name'   => 'attach.user.game.post',
                'target' => $this->userGameService,
                'params' => ['user' => $user, 'game' => $game, 'return' => true],
            ],
            $this->calledEvents[1],
            UserGameServiceDelegator::class . ' did not trigger the event correctly for attach.user.game.post'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallAttachGameWhenEventStops()
    {
        $game = new Game([
            'game_id' => 'foo',
        ]);

        $user = new Child(['user_id' => 'bar']);

        $this->userGameService->shouldReceive('attachGameToUser')
            ->andReturn(true)->never();

        $this->delegator->getEventManager()->attach('attach.user.game', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });

        $this->delegator->attachGameToUser($user, $game);

        $this->assertEquals(
            1,
            count($this->calledEvents),
            UserGameServiceDelegator::class . ' did not trigger the correct number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'attach.user.game',
                'target' => $this->userGameService,
                'params' => ['user' => $user, 'game' => $game],
            ],
            $this->calledEvents[0],
            UserGameServiceDelegator::class . ' did not trigger the event correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldTriggerErrorEventWhenExceptionOnAttachGame()
    {
        $game = new Game([
            'game_id' => 'foo',
        ]);

        $user = new Child(['user_id' => 'bar']);

        $this->userGameService->shouldReceive('attachGameToUser')
            ->andReturnUsing(function () {
                throw new \Exception();
            })->once();

        try {
            $this->delegator->attachGameToUser($user, $game);
            $this->fail('exception not thrown');
        } catch (\Exception $e) {
            $this->assertEquals(
                2,
                count($this->calledEvents),
                UserGameServiceDelegator::class . ' did not trigger the correct number of events'
            );

            $this->assertEquals(
                [
                    'name'   => 'attach.user.game',
                    'target' => $this->userGameService,
                    'params' => ['user' => $user, 'game' => $game],
                ],
                $this->calledEvents[0],
                UserGameServiceDelegator::class . ' did not trigger the event correctly'
            );

            $this->assertEquals(
                [
                    'name'   => 'attach.user.game.error',
                    'target' => $this->userGameService,
                    'params' => ['user' => $user, 'game' => $game, 'exception' => new \Exception()],
                ],
                $this->calledEvents[1],
                UserGameServiceDelegator::class . ' did not trigger the event correctly for attach.user.game.error'
            );
        }
    }

    /**
     * @test
     */
    public function testItShouldCallDetachGameForUser()
    {
        $game = new Game([
            'game_id' => 'foo',
        ]);

        $user = new Child(['user_id' => 'bar']);

        $this->userGameService->shouldReceive('detachGameForUser')
            ->andReturn(true)->once();

        $this->delegator->detachGameForUser($user, $game);

        $this->assertEquals(
            2,
            count($this->calledEvents),
            UserGameServiceDelegator::class . ' did not trigger the correct number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'detach.user.game',
                'target' => $this->userGameService,
                'params' => ['user' => $user, 'game' => $game],
            ],
            $this->calledEvents[0],
            UserGameServiceDelegator::class . ' did not trigger the event correctly'
        );

        $this->assertEquals(
            [
                'name'   => 'detach.user.game.post',
                'target' => $this->userGameService,
                'params' => ['user' => $user, 'game' => $game, 'return' => true],
            ],
            $this->calledEvents[1],
            UserGameServiceDelegator::class . ' did not trigger the event correctly for detach.user.game.post'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallDetachGameWhenEventStops()
    {
        $game = new Game([
            'game_id' => 'foo',
        ]);

        $user = new Child(['user_id' => 'bar']);

        $this->userGameService->shouldReceive('detachGameForUser')
            ->andReturn(true)->never();

        $this->delegator->getEventManager()->attach('detach.user.game', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });

        $this->delegator->detachGameForUser($user, $game);

        $this->assertEquals(
            1,
            count($this->calledEvents),
            UserGameServiceDelegator::class . ' did not trigger the correct number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'detach.user.game',
                'target' => $this->userGameService,
                'params' => ['user' => $user, 'game' => $game],
            ],
            $this->calledEvents[0],
            UserGameServiceDelegator::class . ' did not trigger the event correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldTriggerErrorEventOnExceptionWhileDetachGame()
    {
        $game = new Game([
            'game_id' => 'foo',
        ]);

        $user = new Child(['user_id' => 'bar']);

        $this->userGameService->shouldReceive('detachGameForUser')
            ->andReturnUsing(function () {
                throw new \Exception();
            })->once();

        try {
            $this->delegator->detachGameForUser($user, $game);
            $this->fail('exception not thrown');
        } catch (\Exception $e) {
            $this->assertEquals(
                2,
                count($this->calledEvents),
                UserGameServiceDelegator::class . ' did not trigger the correct number of events'
            );

            $this->assertEquals(
                [
                    'name'   => 'detach.user.game',
                    'target' => $this->userGameService,
                    'params' => ['user' => $user, 'game' => $game],
                ],
                $this->calledEvents[0],
                UserGameServiceDelegator::class . ' did not trigger the event correctly'
            );

            $this->assertEquals(
                [
                    'name'   => 'detach.user.game.error',
                    'target' => $this->userGameService,
                    'params' => ['user' => $user, 'game' => $game, 'exception' => new \Exception()],
                ],
                $this->calledEvents[1],
                UserGameServiceDelegator::class . ' did not trigger the event correctly for detach.user.game.error'
            );
        }
    }

    /**
     * @test
     */
    public function testItShouldCallFetchAllGamesForUser()
    {
        $result = new Iterator(new \ArrayIterator([]));
        $user   = new Child(['user_id' => 'bar']);
        $where  = new Where();
        $where->isNull("g.deleted");

        $this->userGameService->shouldReceive('createWhere')
            ->andReturn($where);

        $this->userGameService->shouldReceive('fetchAllGamesForUser')
            ->andReturn($result)->once();

        $this->assertSame($result, $this->delegator->fetchAllGamesForUser($user));

        $this->assertEquals(
            2,
            count($this->calledEvents),
            UserGameServiceDelegator::class . ' did not trigger the correct number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.user.games',
                'target' => $this->userGameService,
                'params' => ['user' => $user, 'where' => $where, 'prototype' => null],
            ],
            $this->calledEvents[0],
            UserGameServiceDelegator::class . ' did not trigger the event correctly'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.user.games.post',
                'target' => $this->userGameService,
                'params' => ['user' => $user, 'where' => $where, 'prototype' => null, 'games' => $result],
            ],
            $this->calledEvents[1],
            UserGameServiceDelegator::class . ' did not trigger the event correctly for fetch.all.user.games.post'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchAllGamesForUserWhenEventStops()
    {
        $result = new Iterator(new \ArrayIterator());
        $user   = new Child(['user_id' => 'bar']);
        $where  = new Where();

        $this->userGameService->shouldReceive('createWhere')
            ->andReturn($where);

        $this->userGameService->shouldReceive('fetchAllGamesForUser')
            ->andReturn(true)->never();

        $this->delegator
            ->getEventManager()
            ->attach('fetch.all.user.games', function (Event $event) use (&$result) {
                $event->stopPropagation(true);

                return $result;
            });

        $this->assertSame($result, $this->delegator->fetchAllGamesForUser($user));

        $this->assertEquals(
            1,
            count($this->calledEvents),
            UserGameServiceDelegator::class . ' did not trigger the correct number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.user.games',
                'target' => $this->userGameService,
                'params' => ['user' => $user, 'where' => $where, 'prototype' => null],
            ],
            $this->calledEvents[0],
            UserGameServiceDelegator::class . ' did not trigger the event correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldTriggerErrorEventOnExceptionWhileFetchAllUserGames()
    {
        $user  = new Child(['user_id' => 'bar']);
        $where = new Where();

        $this->userGameService->shouldReceive('createWhere')
            ->andReturn($where);
        $where->isNull("g.deleted");
        $exception = new \Exception();
        $this->userGameService->shouldReceive('fetchAllGamesForUser')
            ->andThrow($exception);

        try {
            $this->delegator->fetchAllGamesForUser($user);
            $this->fail('exception not thrown');
        } catch (\Exception $e) {
            $this->assertEquals(
                2,
                count($this->calledEvents),
                UserGameServiceDelegator::class . ' did not trigger the correct number of events'
            );

            $this->assertEquals(
                [
                    'name'   => 'fetch.all.user.games',
                    'target' => $this->userGameService,
                    'params' => ['user' => $user, 'where' => $where, 'prototype' => null],
                ],
                $this->calledEvents[0],
                UserGameServiceDelegator::class . ' did not trigger the event correctly'
            );

            $this->assertEquals(
                [
                    'name'   => 'fetch.all.user.games.error',
                    'target' => $this->userGameService,
                    'params' => [
                        'user'      => $user,
                        'where'     => $where,
                        'prototype' => null,
                        'exception' => $exception,
                    ],
                ],
                $this->calledEvents[1],
                UserGameServiceDelegator::class . ' did not trigger the correct event for fetch.all.user.games.error'
            );
        }
    }

    /**
     * @test
     */
    public function testItShouldCallFetchGameForUser()
    {
        $game = new Game(['game_id' => 'foo']);
        $user = new Child(['user_id' => 'bar']);

        $this->userGameService
            ->shouldReceive('fetchGameForUser')
            ->andReturn($game)->once();

        $this->delegator->fetchGameForUser($user, $game);

        $this->assertEquals(
            2,
            count($this->calledEvents),
            UserGameServiceDelegator::class . ' did not trigger the correct number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.user.game',
                'target' => $this->userGameService,
                'params' => ['user' => $user, 'game' => $game],
            ],
            $this->calledEvents[0],
            UserGameServiceDelegator::class . ' did not trigger the event correctly'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.user.game.post',
                'target' => $this->userGameService,
                'params' => ['user' => $user, 'game' => $game],
            ],
            $this->calledEvents[1],
            UserGameServiceDelegator::class . ' did not trigger the event correctly for fetch.user.game.post'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchGameForUserWhenEventStops()
    {
        $game = new Game([
            'game_id' => 'foo',
        ]);

        $user = new Child(['user_id' => 'bar']);

        $this->userGameService->shouldReceive('fetchGameForUser')
            ->andReturn(true)->never();

        $this->delegator->getEventManager()->attach('fetch.user.game', function (Event $event) use (&$game) {
            $event->stopPropagation(true);

            return $game;
        });

        $this->assertSame($game, $this->delegator->fetchGameForUser($user, $game));

        $this->assertEquals(
            1,
            count($this->calledEvents),
            UserGameServiceDelegator::class . ' did not trigger the correct number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.user.game',
                'target' => $this->userGameService,
                'params' => ['user' => $user, 'game' => $game],
            ],
            $this->calledEvents[0],
            UserGameServiceDelegator::class . ' did not trigger the event correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldTriggerErrorEventOnExceptionWhileFetchingUserGame()
    {
        $game = new Game([
            'game_id' => 'foo',
        ]);

        $user = new Child(['user_id' => 'bar']);

        $this->userGameService->shouldReceive('fetchGameForUser')
            ->andReturnUsing(function () {
                throw new NotFoundException();
            })->once();

        try {
            $this->delegator->fetchGameForUser($user, $game);
            $this->fail('exception is not thrown');
        } catch (\Exception $e) {
            $this->assertEquals(
                2,
                count($this->calledEvents),
                UserGameServiceDelegator::class . ' did not trigger the correct number of events'
            );

            $this->assertEquals(
                [
                    'name'   => 'fetch.user.game',
                    'target' => $this->userGameService,
                    'params' => ['user' => $user, 'game' => $game],
                ],
                $this->calledEvents[0],
                UserGameServiceDelegator::class . ' did not trigger the event correctly'
            );

            $this->assertEquals(
                [
                    'name'   => 'fetch.user.game.error',
                    'target' => $this->userGameService,
                    'params' => ['user' => $user, 'game' => $game, 'exception' => new NotFoundException()],
                ],
                $this->calledEvents[1],
                UserGameServiceDelegator::class . ' did not trigger the event correctly for fetch.user.game.error'
            );
        }
    }
}
