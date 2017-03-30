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
        $where = new Where();
        $this->flipService->shouldReceive('createWhere')->andReturn($where);

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
                'params' => ['where' => $where, 'prototype' => null],
            ],
            $this->calledEvents[0],
            FlipServiceDelegator::class . ' did not trigger the event correctly for fetch.all.flips'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.flips.post',
                'target' => $this->flipService,
                'params' => ['where' => $where, 'prototype' => null, 'flips' => $result],
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
        $where = new Where();
        $this->flipService->shouldReceive('createWhere')->andReturn($where);

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
        $where = new Where();
        $this->flipService->shouldReceive('createWhere')->andReturn($where);

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

    /**
     * @test
     */
    public function testItShouldCallCreateFlip()
    {
        $flip = new Flip(['title' => 'Foo Bar', 'description' => 'baz bat',]);
        $this->flipService->shouldReceive('createFlip')
            ->with($flip)
            ->andReturn(true)
            ->once();
        $this->assertTrue($this->delegator->createFlip($flip));

        $this->assertEquals(
            2,
            count($this->calledEvents),
            FlipServiceDelegator::class . ' did not trigger the correct number of events for createFlip'
        );

        $this->assertEquals(
            [
                'name'   => 'create.flip',
                'target' => $this->flipService,
                'params' => ['flip' => $flip],
            ],
            $this->calledEvents[0],
            FlipServiceDelegator::class . ' did not trigger the event correctly for create.flip'
        );

        $this->assertEquals(
            [
                'name'   => 'create.flip.post',
                'target' => $this->flipService,
                'params' => ['flip' => $flip, 'return' => true],
            ],
            $this->calledEvents[1],
            FlipServiceDelegator::class . ' did not trigger the event correctly for create.flip.post'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallCreateFlipAndTriggerError()
    {
        $exception = new \Exception();
        $flip      = new Flip(['title' => 'Foo Bar', 'description' => 'baz bat',]);
        $this->flipService->shouldReceive('createFlip')
            ->with($flip)
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->createFlip($flip);
            $this->fail(FlipServiceDelegator::class . ' exception was not thrown with createFlip');
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
            FlipServiceDelegator::class . ' did not trigger the correct number of events for createFlip with error'
        );

        $this->assertEquals(
            [
                'name'   => 'create.flip',
                'target' => $this->flipService,
                'params' => ['flip' => $flip],
            ],
            $this->calledEvents[0],
            FlipServiceDelegator::class . ' did not trigger the event correctly for create.flip with error'
        );

        $this->assertEquals(
            [
                'name'   => 'create.flip.error',
                'target' => $this->flipService,
                'params' => ['flip' => $flip, 'error' => $exception],
            ],
            $this->calledEvents[1],
            FlipServiceDelegator::class . ' did not trigger the event correctly for create.flip.error'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallNotCreateFlipWhenEventStops()
    {
        $flip = new Flip();
        $this->flipService->shouldReceive('createFlip')
            ->with($flip)
            ->never();

        $this->delegator->getEventManager()->attach('create.flip', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });

        $this->assertFalse($this->delegator->createFlip($flip));

        $this->assertEquals(
            1,
            count($this->calledEvents),
            FlipServiceDelegator::class . ' did not trigger the correct number of events when create.flip is stopped'
        );

        $this->assertEquals(
            [
                'name'   => 'create.flip',
                'target' => $this->flipService,
                'params' => ['flip' => $flip],
            ],
            $this->calledEvents[0],
            FlipServiceDelegator::class . ' did not trigger the event correctly for create.flip when stopped'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallUpdateFlip()
    {
        $flip = new Flip(['title' => 'Foo Bar', 'description' => 'baz bat']);
        $this->flipService->shouldReceive('updateFlip')
            ->with($flip)
            ->andReturn(true)
            ->once();
        $this->assertTrue($this->delegator->updateFlip($flip));

        $this->assertEquals(
            2,
            count($this->calledEvents),
            FlipServiceDelegator::class . ' did not trigger the correct number of events for updateFlip'
        );

        $this->assertEquals(
            [
                'name'   => 'update.flip',
                'target' => $this->flipService,
                'params' => ['flip' => $flip],
            ],
            $this->calledEvents[0],
            FlipServiceDelegator::class . ' did not trigger the event correctly for update.flip'
        );

        $this->assertEquals(
            [
                'name'   => 'update.flip.post',
                'target' => $this->flipService,
                'params' => ['flip' => $flip, 'return' => true],
            ],
            $this->calledEvents[1],
            FlipServiceDelegator::class . ' did not trigger the event correctly for update.flip.post'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallUpdateFlipAndTriggerError()
    {
        $exception = new \Exception();
        $flip      = new Flip(['title' => 'Foo Bar', 'description' => 'baz bat']);
        $this->flipService->shouldReceive('updateFlip')
            ->with($flip)
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->updateFlip($flip);
            $this->fail(FlipServiceDelegator::class . ' exception was not thrown with updateFlip');
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
            FlipServiceDelegator::class . ' did not trigger the correct number of events for updateFlip with error'
        );

        $this->assertEquals(
            [
                'name'   => 'update.flip',
                'target' => $this->flipService,
                'params' => ['flip' => $flip],
            ],
            $this->calledEvents[0],
            FlipServiceDelegator::class . ' did not trigger the event correctly for update.flip with error'
        );

        $this->assertEquals(
            [
                'name'   => 'update.flip.error',
                'target' => $this->flipService,
                'params' => ['flip' => $flip, 'error' => $exception],
            ],
            $this->calledEvents[1],
            FlipServiceDelegator::class . ' did not trigger the event correctly for update.flip.error'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallNotUpdateFlipWhenEventStops()
    {
        $flip = new Flip();
        $this->flipService->shouldReceive('updateFlip')
            ->with($flip)
            ->never();

        $this->delegator->getEventManager()->attach('update.flip', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });

        $this->assertFalse($this->delegator->updateFlip($flip));

        $this->assertEquals(
            1,
            count($this->calledEvents),
            FlipServiceDelegator::class . ' did not trigger the correct number of events when update.flip is stopped'
        );

        $this->assertEquals(
            [
                'name'   => 'update.flip',
                'target' => $this->flipService,
                'params' => ['flip' => $flip],
            ],
            $this->calledEvents[0],
            FlipServiceDelegator::class . ' did not trigger the event correctly for update.flip when stopped'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallDeleteFlip()
    {
        $flip = new Flip(['title' => 'Foo Bar', 'description' => 'baz bat']);
        $this->flipService->shouldReceive('deleteFlip')
            ->with($flip)
            ->andReturn(true)
            ->once();
        $this->assertTrue($this->delegator->deleteFlip($flip));

        $this->assertEquals(
            2,
            count($this->calledEvents),
            FlipServiceDelegator::class . ' did not trigger the correct number of events for deleteFlip'
        );

        $this->assertEquals(
            [
                'name'   => 'delete.flip',
                'target' => $this->flipService,
                'params' => ['flip' => $flip],
            ],
            $this->calledEvents[0],
            FlipServiceDelegator::class . ' did not trigger the event correctly for delete.flip'
        );

        $this->assertEquals(
            [
                'name'   => 'delete.flip.post',
                'target' => $this->flipService,
                'params' => ['flip' => $flip, 'return' => true],
            ],
            $this->calledEvents[1],
            FlipServiceDelegator::class . ' did not trigger the event correctly for delete.flip.post'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallDeleteFlipAndTriggerError()
    {
        $exception = new \Exception();
        $flip      = new Flip(['title' => 'Foo Bar', 'description' => 'baz bat']);
        $this->flipService->shouldReceive('deleteFlip')
            ->with($flip)
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->deleteFlip($flip);
            $this->fail(FlipServiceDelegator::class . ' exception was not thrown with deleteFlip');
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
            FlipServiceDelegator::class . ' did not trigger the correct number of events for deleteFlip with error'
        );

        $this->assertEquals(
            [
                'name'   => 'delete.flip',
                'target' => $this->flipService,
                'params' => ['flip' => $flip],
            ],
            $this->calledEvents[0],
            FlipServiceDelegator::class . ' did not trigger the event correctly for delete.flip with error'
        );

        $this->assertEquals(
            [
                'name'   => 'delete.flip.error',
                'target' => $this->flipService,
                'params' => ['flip' => $flip, 'error' => $exception],
            ],
            $this->calledEvents[1],
            FlipServiceDelegator::class . ' did not trigger the event correctly for delete.flip.error'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallNotDeleteFlipWhenEventStops()
    {
        $flip = new Flip();
        $this->flipService->shouldReceive('deleteFlip')
            ->with($flip)
            ->never();

        $this->delegator->getEventManager()->attach('delete.flip', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });

        $this->assertFalse($this->delegator->deleteFlip($flip));

        $this->assertEquals(
            1,
            count($this->calledEvents),
            FlipServiceDelegator::class . ' did not trigger the correct number of events when delete.flip is stopped'
        );

        $this->assertEquals(
            [
                'name'   => 'delete.flip',
                'target' => $this->flipService,
                'params' => ['flip' => $flip],
            ],
            $this->calledEvents[0],
            FlipServiceDelegator::class . ' did not trigger the event correctly for delete.flip when stopped'
        );
    }
}
