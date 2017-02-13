<?php

namespace FlipTest\Delegator;

use Flip\Delegator\FlipServiceDelegator;
use Flip\Flip;
use Flip\Service\FlipService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;
use Zend\Paginator\Adapter\Iterator;

/**
 * Test FlipDelegatorTest
 *
 * @group Flip
 * @group Service
 * @group Delegator
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FlipServiceDelegatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|FlipService
     */
    protected $flipService;

    /**
     * @var FlipServiceDelegator
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
        $events             = new EventManager();
        $this->calledEvents = [];
        $this->delegator    = new FlipServiceDelegator($this->flipService, $events);
        $events->attach('*', [$this, 'captureEvents'], PHP_INT_MAX);
    }

    /**
     * @before
     */
    public function setUpFlipService()
    {
        $this->flipService = \Mockery::mock(FlipService::class);
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
        $result = new Iterator(new \ArrayIterator([['foo' => 'bar']]));
        $this->flipService->shouldReceive('fetchAll')
            ->andReturn($result)
            ->once();

        $this->delegator->fetchAll();

        $this->assertEquals(
            2,
            count($this->calledEvents),
            FlipServiceDelegator::class . ' did not trigger the correct number of events when fetching all'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.flips',
                'target' => $this->flipService,
                'params' => ['where' => new Where(), 'prototype' => null],
            ],
            $this->calledEvents[0],
            FlipServiceDelegator::class . ' did not trigger the event correctly for fetch.all.flips'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.flips.post',
                'target' => $this->flipService,
                'params' => ['where' => new Where(), 'prototype' => null, 'flips' => $result],
            ],
            $this->calledEvents[1],
            FlipServiceDelegator::class . ' did not trigger the event correctly for fetch.all.flips.post'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchAllAndTriggerEventOnError()
    {
        $exception = new \Exception();
        $this->flipService->shouldReceive('fetchAll')
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->fetchAll();
            $this->fail(FlipServiceDelegator::class . ' exception was not thrown with fetchAll');
        } catch (\Throwable $actual) {
            $this->assertSame(
                $exception,
                $actual,
                FlipServiceDelegator::class . ' did not re-throw the same exception'
            );
        }

        $this->assertEquals(
            2,
            count($this->calledEvents),
            FlipServiceDelegator::class . ' did not trigger the correct number of events when fetching all with error'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.flips',
                'target' => $this->flipService,
                'params' => ['where' => new Where(), 'prototype' => null],
            ],
            $this->calledEvents[0],
            FlipServiceDelegator::class . ' did not trigger the event correctly for fetch.all.flips with error'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.flips.error',
                'target' => $this->flipService,
                'params' => ['where' => new Where(), 'prototype' => null, 'error' => $exception],
            ],
            $this->calledEvents[1],
            FlipServiceDelegator::class . ' did not trigger the event correctly for fetch.all.flips.error'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallNotFetchAllWhenEventStops()
    {
        $result = new Iterator(new \ArrayIterator([['foo' => 'bar']]));
        $this->flipService->shouldReceive('fetchAll')
            ->never();

        $this->delegator->getEventManager()->attach('fetch.all.flips', function (Event $event) use (&$result) {
            $event->stopPropagation(true);

            return $result;
        });

        $this->assertSame(
            $result,
            $this->delegator->fetchAll(),
            FlipServiceDelegator::class . ' did not return the event response'
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            FlipServiceDelegator::class . ' did not trigger correct number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.flips',
                'target' => $this->flipService,
                'params' => ['where' => new Where(), 'prototype' => null],
            ],
            $this->calledEvents[0],
            FlipServiceDelegator::class . ' did not trigger the event correctly for fetch.all.flips'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchFlipById()
    {
        $result = new Flip();
        $this->flipService->shouldReceive('fetchFlipById')
            ->andReturn($result)
            ->once();

        $this->delegator->fetchFlipById('foo-bar');

        $this->assertEquals(
            2,
            count($this->calledEvents),
            FlipServiceDelegator::class . ' did not trigger the correct number of events for fetchFlipById'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.flip',
                'target' => $this->flipService,
                'params' => ['flip_id' => 'foo-bar'],
            ],
            $this->calledEvents[0],
            FlipServiceDelegator::class . ' did not trigger the event correctly for fetch.flip'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.flip.post',
                'target' => $this->flipService,
                'params' => ['flip_id' => 'foo-bar', 'flip' => $result],
            ],
            $this->calledEvents[1],
            FlipServiceDelegator::class . ' did not trigger the event correctly for fetch.flip.post'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchFlipByIdAndTriggerError()
    {
        $exception = new \Exception();
        $this->flipService->shouldReceive('fetchFlipById')
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->fetchFlipById('foo-bar');
            $this->fail(FlipServiceDelegator::class . ' exception was not thrown with fetchFlipById');
        } catch (\Throwable $actual) {
            $this->assertSame(
                $exception,
                $actual,
                FlipServiceDelegator::class . ' did not re-throw the same exception'
            );
        }

        $this->assertEquals(
            2,
            count($this->calledEvents),
            FlipServiceDelegator::class . ' did not trigger the correct number of events for fetchFlipById with error'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.flip',
                'target' => $this->flipService,
                'params' => ['flip_id' => 'foo-bar'],
            ],
            $this->calledEvents[0],
            FlipServiceDelegator::class . ' did not trigger the event correctly for fetch.flip with error'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.flip.error',
                'target' => $this->flipService,
                'params' => ['flip_id' => 'foo-bar', 'error' => $exception],
            ],
            $this->calledEvents[1],
            FlipServiceDelegator::class . ' did not trigger the event correctly for fetch.flip.error'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallNotFetchFlipByIdWhenEventStops()
    {
        $flip = new Flip();
        $this->flipService->shouldReceive('fetchFlipById')
            ->never();

        $this->delegator->getEventManager()->attach('fetch.flip', function (Event $event) use (&$flip) {
            $event->stopPropagation(true);

            return $flip;
        });

        $this->assertEquals(
            $flip,
            $this->delegator->fetchFlipById('foo-bar'),
            FlipServiceDelegator::class . ' did not return the flip from the event'
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            FlipServiceDelegator::class . ' did not trigger the correct number of events when fetch.flip is stopped'
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.flip',
                'target' => $this->flipService,
                'params' => ['flip_id' => 'foo-bar'],
            ],
            $this->calledEvents[0],
            FlipServiceDelegator::class . ' did not trigger the event correctly for fetch.flip when stopped'
        );
    }
}
