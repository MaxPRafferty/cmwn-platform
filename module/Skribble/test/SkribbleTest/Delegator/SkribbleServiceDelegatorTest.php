<?php

namespace SkribbleTest\Delegator;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Skribble\Service\SkribbleService;
use Skribble\Skribble;
use Skribble\Delegator\SkribbleServiceDelegator;
use Skribble\Service\SkribbleServiceInterface;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;

/**
 * Test SkribbleServiceDelegatorTest
 *
 * @group Skribble
 * @group SkribbleService
 * @group Service
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SkribbleServiceDelegatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|\Skribble\Service\SkribbleService
     */
    protected $skribbleService;

    /**
     * @var SkribbleServiceDelegator
     */
    protected $delegator;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @var Skribble
     */
    protected $skribble;

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->calledEvents = [];
        $this->delegator    = new SkribbleServiceDelegator($this->skribbleService, new EventManager());
        $this->delegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->skribbleService = \Mockery::mock(SkribbleService::class);
    }

    /**
     * @before
     */
    public function setUpSkribble()
    {
        $this->skribble = new Skribble();
        $this->skribble->setSkribbleId('foo-bar');
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
    public function testItShouldCreateSkribble()
    {
        $this->skribbleService->shouldReceive('createSkribble')
            ->with($this->skribble)
            ->andReturn(true)
            ->once();

        $this->delegator->createSkribble($this->skribble);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'create.skribble',
                'target' => $this->skribbleService,
                'params' => ['skribble' => $this->skribble],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'create.skribble.post',
                'target' => $this->skribbleService,
                'params' => ['skribble' => $this->skribble],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldCreateSkribbleWithException()
    {
        $exception = new \Exception('test');
        $this->skribbleService->shouldReceive('createSkribble')
            ->with($this->skribble)
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->createSkribble($this->skribble);
            $this->fail('Exception was not re thrown');
        } catch (\Exception $createException) {
            // noop
        }

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'create.skribble',
                'target' => $this->skribbleService,
                'params' => ['skribble' => $this->skribble],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'create.skribble.error',
                'target' => $this->skribbleService,
                'params' => ['skribble' => $this->skribble, 'error' => $exception],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldUpdateSkribble()
    {
        $this->skribbleService->shouldReceive('updateSkribble')
            ->with($this->skribble)
            ->andReturn(true)
            ->once();

        $this->delegator->updateSkribble($this->skribble);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'update.skribble',
                'target' => $this->skribbleService,
                'params' => ['skribble' => $this->skribble],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'update.skribble.post',
                'target' => $this->skribbleService,
                'params' => ['skribble' => $this->skribble],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldUpdateSkribbleWithException()
    {
        $exception = new \Exception('test');
        $this->skribbleService->shouldReceive('updateSkribble')
            ->with($this->skribble)
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->updateSkribble($this->skribble);
            $this->fail('Exception was not re thrown');
        } catch (\Exception $updateException) {
            // noop
        }

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'update.skribble',
                'target' => $this->skribbleService,
                'params' => ['skribble' => $this->skribble],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'update.skribble.error',
                'target' => $this->skribbleService,
                'params' => ['skribble' => $this->skribble, 'error' => $exception],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldDeleteSkribble()
    {
        $this->skribbleService->shouldReceive('deleteSkribble')
            ->with($this->skribble, false)
            ->andReturn(true)
            ->once();

        $this->delegator->deleteSkribble($this->skribble);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.skribble',
                'target' => $this->skribbleService,
                'params' => ['skribble' => $this->skribble, 'hard' => false],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'delete.skribble.post',
                'target' => $this->skribbleService,
                'params' => ['skribble' => $this->skribble, 'hard' => false],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldDeleteSkribbleWithException()
    {
        $exception = new \Exception('test');
        $this->skribbleService->shouldReceive('deleteSkribble')
            ->with($this->skribble, false)
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->deleteSkribble($this->skribble);
            $this->fail('Exception was not re thrown');
        } catch (\Exception $deleteException) {
            // noop
        }

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.skribble',
                'target' => $this->skribbleService,
                'params' => ['skribble' => $this->skribble, 'hard' => false],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'delete.skribble.error',
                'target' => $this->skribbleService,
                'params' => ['skribble' => $this->skribble, 'hard' => false, 'error' => $exception],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllSkribbles()
    {
        $result = new \ArrayIterator([['foo' => 'bar']]);
        $this->skribbleService->shouldReceive('fetchAllForUser')
            ->andReturn($result)
            ->once();

        $where = new Where();
        $this->assertEquals($result, $this->delegator->fetchAllForUser('foo-bar', $where));

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.skribbles',
                'target' => $this->skribbleService,
                'params' => ['user' => 'foo-bar', 'where' => $where, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.skribbles.post',
                'target' => $this->skribbleService,
                'params' => ['user' => 'foo-bar', 'where' => $where, 'prototype' => null, 'result' => $result],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchReceivedForUser()
    {
        $result = new \ArrayIterator([['foo' => 'bar']]);
        $this->skribbleService->shouldReceive('fetchReceivedForUser')
            ->andReturn($result)
            ->once();

        $where = new Where();
        $this->assertEquals($result, $this->delegator->fetchReceivedForUser('foo-bar', $where));

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.received.skribbles',
                'target' => $this->skribbleService,
                'params' => ['user' => 'foo-bar', 'where' => $where, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.received.skribbles.post',
                'target' => $this->skribbleService,
                'params' => ['user' => 'foo-bar', 'where' => $where, 'prototype' => null, 'result' => $result],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchSentForUser()
    {
        $result = new \ArrayIterator([['foo' => 'bar']]);
        $this->skribbleService->shouldReceive('fetchSentForUser')
            ->andReturn($result)
            ->once();

        $where = new Where();
        $this->assertEquals($result, $this->delegator->fetchSentForUser('foo-bar', $where));

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.sent.skribbles',
                'target' => $this->skribbleService,
                'params' => ['user' => 'foo-bar', 'where' => $where, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.sent.skribbles.post',
                'target' => $this->skribbleService,
                'params' => ['user' => 'foo-bar', 'where' => $where, 'prototype' => null, 'result' => $result],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchDraftForUser()
    {
        $result = new \ArrayIterator([['foo' => 'bar']]);
        $this->skribbleService->shouldReceive('fetchDraftForUser')
            ->andReturn($result)
            ->once();

        $where = new Where();
        $this->assertEquals($result, $this->delegator->fetchDraftForUser('foo-bar', $where));

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.draft.skribbles',
                'target' => $this->skribbleService,
                'params' => ['user' => 'foo-bar', 'where' => $where, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.draft.skribbles.post',
                'target' => $this->skribbleService,
                'params' => ['user' => 'foo-bar', 'where' => $where, 'prototype' => null, 'result' => $result],
            ],
            $this->calledEvents[1]
        );
    }
}
