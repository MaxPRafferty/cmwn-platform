<?php

namespace FlipTest\Delegator;

use Flip\Delegator\FlipDelegator;
use Flip\Flip;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;

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
 */
class FlipDelegatorTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Flip\Service\FlipService
     */
    protected $flipService;

    /**
     * @var FlipDelegator
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
        $this->flipService = \Mockery::mock('\Flip\Service\FlipService');
    }

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->calledEvents = [];
        $this->delegator = new FlipDelegator($this->flipService);
        $this->delegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }

    /**
     * @param Event $event
     */
    public function captureEvents(Event $event)
    {
        $this->calledEvents[] = [
            'name'   => $event->getName(),
            'target' => $event->getTarget(),
            'params' => $event->getParams()
        ];
    }

    /**
     * @test
     */
    public function testItShouldCallFetchAll()
    {
        $result = new \ArrayIterator([['foo' => 'bar']]);
        $this->flipService->shouldReceive('fetchAll')
            ->andReturn($result)
            ->once();

        $this->delegator->fetchAll();

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.flips',
                'target' => $this->flipService,
                'params' => ['where' => new Where(), 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.all.flips.post',
                'target' => $this->flipService,
                'params' => ['where' => new Where(), 'prototype' => null, 'flips' => $result],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallNotFetchAllWhenEventStops()
    {
        $this->flipService->shouldReceive('fetchAll')
            ->never();

        $this->delegator->getEventManager()->attach('fetch.all.flips', function (Event $event) {
            $event->stopPropagation(true);
            return 'foo-bar';
        });

        $this->assertEquals('foo-bar', $this->delegator->fetchAll());

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.flips',
                'target' => $this->flipService,
                'params' => ['where' => new Where(), 'prototype' => null],
            ],
            $this->calledEvents[0]
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

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.flip',
                'target' => $this->flipService,
                'params' => ['flip_id' => 'foo-bar'],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.flip.post',
                'target' => $this->flipService,
                'params' => ['flip_id' => 'foo-bar', 'flip' => $result],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallNotFetchFlipByIdWhenEventStops()
    {
        $this->flipService->shouldReceive('fetchFlipById')
            ->never();

        $this->delegator->getEventManager()->attach('fetch.flip', function (Event $event) {
            $event->stopPropagation(true);
            return 'foo-bar';
        });

        $this->assertEquals('foo-bar', $this->delegator->fetchFlipById('foo-bar'));

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.flip',
                'target' => $this->flipService,
                'params' => ['flip_id' => 'foo-bar'],
            ],
            $this->calledEvents[0]
        );
    }
}
