<?php

namespace FlipTest\Delegator;

use Flip\Delegator\FlipUserServiceDelegator;
use Flip\Service\FlipUserService;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Delegator\UserServiceDelegator;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;
use Zend\Paginator\Adapter\Iterator;

/**
 * Test FlipUserDelegatorTest
 *
 * @group Flip
 * @group User
 * @group Service
 * @group Delegator
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FlipUserServiceDelegatorTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|FlipUserService
     */
    protected $flipService;

    /**
     * @var FlipUserServiceDelegator
     */
    protected $delegator;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @before
     */
    public function setUpFlipService()
    {
        $this->flipService = \Mockery::mock(FlipUserService::class);
    }

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $events             = new EventManager();
        $this->calledEvents = [];
        $this->delegator    = new FlipUserServiceDelegator($this->flipService, $events);
        $events->attach('*', [$this, 'captureEvents'], 1000000);
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
    public function testItShouldCallFetchAllEarnedFlipsForUser()
    {
        $result = new Iterator(new \ArrayIterator([['foo' => 'bar']]));
        $this->flipService->shouldReceive('fetchEarnedFlipsForUser')
            ->andReturn($result)
            ->once();

        $this->assertEquals(
            $result,
            $this->delegator->fetchEarnedFlipsForUser('foo-bar'),
            'Flip User Service did not return the result from the real service'
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.user.flips',
                'target' => $this->flipService,
                'params' => ['where' => new Where(), 'prototype' => null, 'user' => 'foo-bar'],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.user.flips.post',
                'target' => $this->flipService,
                'params' => ['where' => new Where(), 'prototype' => null, 'user' => 'foo-bar', 'flips' => $result],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchAllEarnedFlipsForUserAndTriggerError()
    {
        $exception = new \Exception();
        $this->flipService->shouldReceive('fetchEarnedFlipsForUser')
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->fetchEarnedFlipsForUser('foo-bar');
            $this->fail(UserServiceDelegator::class . ' failed to throw exception from service');
        } catch (\Throwable $actual) {
            $this->assertSame(
                $exception,
                $actual,
                UserServiceDelegator::class . ' failed to re-throw the same exception from service'
            );
        }

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.user.flips',
                'target' => $this->flipService,
                'params' => ['where' => new Where(), 'prototype' => null, 'user' => 'foo-bar'],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.user.flips.error',
                'target' => $this->flipService,
                'params' => ['where' => new Where(), 'prototype' => null, 'user' => 'foo-bar', 'error' => $exception],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchAllEarnedFlipsForUser()
    {
        $result = new Iterator(new \ArrayIterator([['foo' => 'bar']]));
        $this->flipService->shouldReceive('fetchEarnedFlipsForUser')
            ->never();

        $this->delegator->getEventManager()->attach('fetch.user.flips', function (Event $event) use (&$result) {
            $event->stopPropagation(true);

            return $result;
        });

        $this->assertEquals(
            $result,
            $this->delegator->fetchEarnedFlipsForUser('foo-bar'),
            'Flip User Service did not return the result from the real service'
        );

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.user.flips',
                'target' => $this->flipService,
                'params' => ['where' => new Where(), 'prototype' => null, 'user' => 'foo-bar'],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallAttachFlipToUser()
    {
        $this->flipService->shouldReceive('attachFlipToUser')
            ->with('foo-bar', 'baz-bat')
            ->once()
            ->andReturn(true);

        $this->assertEquals(
            true,
            $this->delegator->attachFlipToUser('foo-bar', 'baz-bat'),
            'Flip User Service did not return the result from the real service'
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'attach.flip',
                'target' => $this->flipService,
                'params' => ['flip' => 'baz-bat', 'user' => 'foo-bar'],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'attach.flip.post',
                'target' => $this->flipService,
                'params' => ['flip' => 'baz-bat', 'user' => 'foo-bar'],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallAttachFlipToUserAndTriggerError()
    {
        $exception = new \Exception();
        $this->flipService->shouldReceive('attachFlipToUser')
            ->with('foo-bar', 'baz-bat')
            ->once()
            ->andThrow($exception);

        try {
            $this->delegator->attachFlipToUser('foo-bar', 'baz-bat');
            $this->fail(UserServiceDelegator::class . ' failed to throw exception from service');
        } catch (\Throwable $actual) {
            $this->assertSame(
                $exception,
                $actual,
                UserServiceDelegator::class . ' failed to re-throw the same exception from service'
            );
        }
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'attach.flip',
                'target' => $this->flipService,
                'params' => ['flip' => 'baz-bat', 'user' => 'foo-bar'],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'attach.flip.error',
                'target' => $this->flipService,
                'params' => ['flip' => 'baz-bat', 'user' => 'foo-bar', 'error' => $exception],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallAttachFlipToUser()
    {
        $this->flipService->shouldReceive('attachFlipToUser')
            ->never();

        $this->delegator->getEventManager()->attach('attach.flip', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });

        $this->assertEquals(
            false,
            $this->delegator->attachFlipToUser('foo-bar', 'baz-bat'),
            'Flip User Service did not return the result from the real service'
        );

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'attach.flip',
                'target' => $this->flipService,
                'params' => ['flip' => 'baz-bat', 'user' => 'foo-bar'],
            ],
            $this->calledEvents[0]
        );
    }
}
