<?php

namespace FlagTest\Delegator;

use Application\Exception\NotFoundException;
use Flag\Delegator\FlagDelegator;
use Flag\Flag;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;

/**
 * Class FlagDelegatorTest
 *
 * @package FlagTest
 * @group   Delegator
 * @group   Flag
 * @group   FlagDelegator
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FlagDelegatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|\Flag\Service\FlagService
     */
    protected $flagService;

    /**
     * @var FlagDelegator
     */
    protected $delegator;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @before
     */
    public function setUpFlagService()
    {
        $this->flagService = \Mockery::mock('Flag\Service\FlagService');
    }

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $events = new EventManager();
        $this->calledEvents = [];
        $this->delegator    = new FlagDelegator($this->flagService, $events);
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
            'params' => $event->getParams(),
        ];
    }

    /**
     * @test
     */
    public function testItShouldCallFetchAll()
    {
        $result = new \ArrayIterator([]);

        $this->flagService
            ->shouldReceive('fetchAll')
            ->andReturn($result);
        $this->delegator->fetchAll();
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.flagged.images',
                'target' => $this->flagService,
                'params' => ['where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.all.flagged.images.post',
                'target' => $this->flagService,
                'params' => ['where' => null, 'prototype' => null, 'flagged-images' => $result],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchAllWhenEvenStops()
    {
        $this->flagService
            ->shouldReceive('fetchAll')
            ->never();
        $this->delegator->getEventManager()->attach('fetch.all.flagged.images', function (Event $event) {
            $event->stopPropagation(true);

            return 'foo';
        });

        $this->assertEquals('foo', $this->delegator->fetchAll());

        $this->assertEquals(1, count($this->calledEvents));

        $this->assertEquals(
            [
                'name'   => 'fetch.all.flagged.images',
                'target' => $this->flagService,
                'params' => ['where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchFlag()
    {
        $result = new Flag();

        $this->flagService
            ->shouldReceive('fetchFlag')
            ->andReturn($result);
        $this->delegator->fetchFlag('qwerty');
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.flagged.image',
                'target' => $this->flagService,
                'params' => ['flag_id' => 'qwerty', 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.flagged.image.post',
                'target' => $this->flagService,
                'params' => ['flag_id' => 'qwerty', 'prototype' => null, 'flagged-image' => $result],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldErrorWhenFlagNotFound()
    {
        $exception = new NotFoundException('No Flagged Image Found');
        $this->flagService->shouldReceive('fetchFlag')
            ->andThrow($exception);
        try {
            $this->delegator->fetchFlag('qwerty');
        } catch (NotFoundException $nf) {
            //noop
        }

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.flagged.image',
                'target' => $this->flagService,
                'params' => ['flag_id' => 'qwerty', 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.flagged.image.error',
                'target' => $this->flagService,
                'params' => [
                    'flag_id'   => 'qwerty',
                    'prototype' => null,
                    'exception' => 'No Flagged Image Found',
                ],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchFlagWhenEventStops()
    {
        $this->flagService
            ->shouldReceive('fetchFlag')
            ->never();
        $this->delegator->getEventManager()->attach('fetch.flagged.image', function (Event $event) {
            $event->stopPropagation(true);

            return 'foo';
        });

        $this->assertEquals('foo', $this->delegator->fetchFlag('qwerty'));

        $this->assertEquals(1, count($this->calledEvents));

        $this->assertEquals(
            [
                'name'   => 'fetch.flagged.image',
                'target' => $this->flagService,
                'params' => ['flag_id' => 'qwerty', 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallSaveFlag()
    {
        $flag = new Flag();
        $this->flagService->shouldReceive('saveFlag')
            ->with($flag)
            ->andReturn(true);
        $this->assertTrue($this->delegator->saveFlag($flag));
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'save.flagged.image',
                'target' => $this->flagService,
                'params' => ['flag-data' => $flag],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'save.flagged.image.post',
                'target' => $this->flagService,
                'params' => ['flag-data' => $flag],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallSaveFlagWhenEventStops()
    {
        $flag = new Flag();
        $this->flagService->shouldReceive('saveFlag')
            ->with($flag)
            ->andReturn(true);
        $this->delegator->getEventManager()->attach('save.flagged.image', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });
        $this->assertFalse($this->delegator->saveFlag($flag));
        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'save.flagged.image',
                'target' => $this->flagService,
                'params' => ['flag-data' => $flag],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallUpdateFlag()
    {
        $flag = new Flag();
        $this->flagService->shouldReceive('updateFlag')
            ->with($flag)
            ->andReturn(true);
        $this->assertTrue($this->delegator->updateFlag($flag));
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'update.flagged.image',
                'target' => $this->flagService,
                'params' => ['flag-data' => $flag],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'update.flagged.image.post',
                'target' => $this->flagService,
                'params' => ['flag-data' => $flag],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallUpdateFlagWhenEventStops()
    {
        $flag = new Flag();
        $this->flagService->shouldReceive('updateFlag')
            ->with($flag);
        $this->delegator->getEventManager()->attach('update.flagged.image', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });
        $this->assertFalse($this->delegator->updateFlag($flag));
        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'update.flagged.image',
                'target' => $this->flagService,
                'params' => ['flag-data' => $flag],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallDeleteFlag()
    {
        $flag = new Flag();
        $this->flagService->shouldReceive('deleteFlag')
            ->with($flag)
            ->andReturn(true);
        $this->assertTrue($this->delegator->deleteFlag($flag));
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.flagged.image',
                'target' => $this->flagService,
                'params' => ['flag-data' => $flag],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'delete.flagged.image.post',
                'target' => $this->flagService,
                'params' => ['flag-data' => $flag],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallDeleteFlagWhenEventStops()
    {
        $flag = new Flag();
        $this->flagService->shouldReceive('deleteFlag')
            ->with($flag);
        $this->delegator->getEventManager()->attach('delete.flagged.image', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });
        $this->assertFalse($this->delegator->deleteFlag($flag));
        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.flagged.image',
                'target' => $this->flagService,
                'params' => ['flag-data' => $flag],
            ],
            $this->calledEvents[0]
        );
    }
}
