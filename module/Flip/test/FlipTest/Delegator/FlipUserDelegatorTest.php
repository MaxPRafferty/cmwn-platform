<?php

namespace FlipTest\Delegator;

use Flip\Delegator\FlipUserDelegator;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;

/**
 * Test FlipUserDelegatorTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FlipUserDelegatorTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Flip\Service\FlipUserService
     */
    protected $flipService;

    /**
     * @var FlipUserDelegator
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
        $this->flipService = \Mockery::mock('\Flip\Service\FlipUserService');
    }

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->calledEvents = [];
        $this->delegator = new FlipUserDelegator($this->flipService);
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
    public function testItShouldCallFetchAllEarnedFlipsForUser()
    {
        $result = new \ArrayIterator([['foo' => 'bar']]);
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
    public function testItShouldNotCallFetchAllEarnedFlipsForUser()
    {
        $result = new \ArrayIterator([['foo' => 'bar']]);
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
