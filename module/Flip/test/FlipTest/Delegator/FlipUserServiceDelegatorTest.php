<?php

namespace FlipTest\Delegator;

use Application\Exception\NotFoundException;
use Flip\Delegator\FlipUserServiceDelegator;
use Flip\EarnedFlip;
use Flip\Service\FlipUserService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use User\Child;
use User\PlaceHolder;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;
use Zend\Paginator\Adapter\Iterator;

/**
 * Test FlipUserDelegatorTest
 */
class FlipUserServiceDelegatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

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
    public function setUpDelegator()
    {
        $events             = new EventManager();
        $this->calledEvents = [];
        $this->delegator    = new FlipUserServiceDelegator($this->flipService, $events);
        $events->attach('*', [$this, 'captureEvents'], 1000000);
    }

    /**
     * @before
     */
    public function setUpFlipService()
    {
        $this->flipService = \Mockery::mock(FlipUserService::class);
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
        $where = new Where();
        $this->flipService->shouldReceive('createWhere')
            ->andReturn($where);

        $result = new Iterator(new \ArrayIterator([['foo' => 'bar']]));
        $this->flipService->shouldReceive('fetchEarnedFlipsForUser')
            ->with('foo-bar', $where, null)
            ->andReturn($result)
            ->once();

        $this->assertEquals(
            $result,
            $this->delegator->fetchEarnedFlipsForUser('foo-bar'),
            FlipUserServiceDelegator::class . ' did not return the result from the real service'
        );

        $this->assertEquals(
            2,
            count($this->calledEvents),
            FlipUserServiceDelegator::class . ' did not trigger the correct number of expected events'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.user.flips',
                'target' => $this->flipService,
                'params' => ['where' => $where, 'prototype' => null, 'user' => 'foo-bar'],
            ],
            $this->calledEvents[0],
            FlipUserServiceDelegator::class . ' did not trigger fetch.user.flips correctly'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.user.flips.post',
                'target' => $this->flipService,
                'params' => ['where' => new Where(), 'prototype' => null, 'user' => 'foo-bar', 'flips' => $result],
            ],
            $this->calledEvents[1],
            FlipUserServiceDelegator::class . ' did not trigger fetch.user.flips.post correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchAllEarnedFlipsForUserAndTriggerError()
    {
        $where = new Where();
        $this->flipService->shouldReceive('createWhere')
            ->andReturn($where);

        $exception = new \Exception();
        $this->flipService->shouldReceive('fetchEarnedFlipsForUser')
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->fetchEarnedFlipsForUser('foo-bar');
            $this->fail(FlipUserServiceDelegator::class . ' failed to throw exception from service');
        } catch (\Throwable $actual) {
            $this->assertSame(
                $exception,
                $actual,
                FlipUserServiceDelegator::class . ' failed to re-throw the same exception from service'
            );
        }

        $this->assertEquals(
            2,
            count($this->calledEvents),
            FlipUserServiceDelegator::class . ' did not trigger the correct number of expected events on an error'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.user.flips',
                'target' => $this->flipService,
                'params' => ['where' => $where, 'prototype' => null, 'user' => 'foo-bar'],
            ],
            $this->calledEvents[0],
            FlipUserServiceDelegator::class . ' did not trigger fetch.user.flips correctly during an error'
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.user.flips.error',
                'target' => $this->flipService,
                'params' => ['where' => $where, 'prototype' => null, 'user' => 'foo-bar', 'error' => $exception],
            ],
            $this->calledEvents[1],
            FlipUserServiceDelegator::class . ' did not trigger fetch.user.flips correctly during an error'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchAllEarnedFlipsForUser()
    {
        $where = new Where();
        $this->flipService->shouldReceive('createWhere')
            ->andReturn($where);

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
            FlipUserServiceDelegator::class . ' did not return the result from the event'
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            FlipUserServiceDelegator::class . ' triggered the incorrect number of events when the fetch.user.flips stops'
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.user.flips',
                'target' => $this->flipService,
                'params' => ['where' => $where, 'prototype' => null, 'user' => 'foo-bar'],
            ],
            $this->calledEvents[0],
            FlipUserServiceDelegator::class . ' did not trigger fetch.user.flips correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallAttachFlipToUser()
    {
        $user = new PlaceHolder();
        $user->setUserId('foo-bar');

        $this->flipService->shouldReceive('attachFlipToUser')
            ->with($user, 'baz-bat')
            ->once()
            ->andReturn(true);

        $this->assertEquals(
            true,
            $this->delegator->attachFlipToUser($user, 'baz-bat'),
            'Flip User Service did not return the result from the real service'
        );

        $this->assertEquals(
            2,
            count($this->calledEvents),
            FlipUserServiceDelegator::class . ' called the incorrect number of events for attachFlipToUser'
        );

        $this->assertEquals(
            [
                'name'   => 'attach.flip',
                'target' => $this->flipService,
                'params' => ['flip' => 'baz-bat', 'user' => $user],
            ],
            $this->calledEvents[0],
            FlipUserServiceDelegator::class . ' did not trigger attach.flip correctly'
        );

        $this->assertEquals(
            [
                'name'   => 'attach.flip.post',
                'target' => $this->flipService,
                'params' => ['flip' => 'baz-bat', 'user' => $user],
            ],
            $this->calledEvents[1],
            FlipUserServiceDelegator::class . ' did not trigger attach.flip.post correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallAttachFlipToUserAndTriggerError()
    {
        $user = new PlaceHolder();
        $user->setUserId('foo-bar');

        $exception = new \Exception();
        $this->flipService->shouldReceive('attachFlipToUser')
            ->with($user, 'baz-bat')
            ->once()
            ->andThrow($exception);

        try {
            $this->delegator->attachFlipToUser($user, 'baz-bat');
            $this->fail(FlipUserServiceDelegator::class . ' failed to throw exception from service');
        } catch (\Throwable $actual) {
            $this->assertSame(
                $exception,
                $actual,
                FlipUserServiceDelegator::class . ' failed to re-throw the same exception from service'
            );
        }

        $this->assertEquals(
            2,
            count($this->calledEvents),
            FlipUserServiceDelegator::class . ' did not trigger the correct number of events during an error'
        );

        $this->assertEquals(
            [
                'name'   => 'attach.flip',
                'target' => $this->flipService,
                'params' => ['flip' => 'baz-bat', 'user' => $user],
            ],
            $this->calledEvents[0],
            FlipUserServiceDelegator::class . ' did not trigger attach.flip correctly for error'
        );
        $this->assertEquals(
            [
                'name'   => 'attach.flip.error',
                'target' => $this->flipService,
                'params' => ['flip' => 'baz-bat', 'user' => $user, 'error' => $exception],
            ],
            $this->calledEvents[1],
            FlipUserServiceDelegator::class . ' did not trigger attach.flip.error correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallAttachFlipToUser()
    {
        $user = new PlaceHolder();
        $user->setUserId('foo-bar');

        $this->flipService->shouldReceive('attachFlipToUser')
            ->never();

        $this->delegator->getEventManager()->attach('attach.flip', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });

        $this->assertEquals(
            false,
            $this->delegator->attachFlipToUser($user, 'baz-bat'),
            FlipUserServiceDelegator::class . ' did not return the result from the attach.flip event'
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            FlipUserServiceDelegator::class . ' triggered the incorrect number of events when attach.flip stops'
        );

        $this->assertEquals(
            [
                'name'   => 'attach.flip',
                'target' => $this->flipService,
                'params' => ['flip' => 'baz-bat', 'user' => $user],
            ],
            $this->calledEvents[0],
            FlipUserServiceDelegator::class . ' did not trigger attach.flip.error correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallAcknowledgeFlip()
    {
        $earnedFlip = new EarnedFlip();

        $this->flipService->shouldReceive('acknowledgeFlip')
            ->with($earnedFlip)
            ->once()
            ->andReturn(true);

        $this->assertTrue(
            $this->delegator->acknowledgeFlip($earnedFlip),
            FlipUserServiceDelegator::class . ' did not return the result from the service'
        );

        $this->assertEquals(
            2,
            count($this->calledEvents),
            FlipUserServiceDelegator::class . ' triggered the incorrect number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'acknowledge.flip',
                'target' => $this->flipService,
                'params' => ['earned_flip' => $earnedFlip],
            ],
            $this->calledEvents[0],
            FlipUserServiceDelegator::class . ' did not trigger acknowledge.flip correctly'
        );

        $this->assertEquals(
            [
                'name'   => 'acknowledge.flip.post',
                'target' => $this->flipService,
                'params' => ['earned_flip' => $earnedFlip],
            ],
            $this->calledEvents[1],
            FlipUserServiceDelegator::class . ' did not trigger acknowledge.flip.post correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallAcknowledgeFlipAndTriggerError()
    {
        $earnedFlip = new EarnedFlip();
        $exception  = new \Exception();
        $this->flipService->shouldReceive('acknowledgeFlip')
            ->with($earnedFlip)
            ->once()
            ->andThrow($exception);

        try {
            $this->delegator->acknowledgeFlip($earnedFlip);
            $this->fail(FlipUserServiceDelegator::class . ' failed to throw exception from service');
        } catch (\Throwable $actual) {
            $this->assertSame(
                $exception,
                $actual,
                FlipUserServiceDelegator::class . ' failed to re-throw the same exception from service'
            );
        }
        $this->assertEquals(
            2,
            count($this->calledEvents),
            FlipUserServiceDelegator::class . ' triggered the incorrect number of events on error'
        );

        $this->assertEquals(
            [
                'name'   => 'acknowledge.flip',
                'target' => $this->flipService,
                'params' => ['earned_flip' => $earnedFlip],
            ],
            $this->calledEvents[0],
            FlipUserServiceDelegator::class . ' did not trigger acknowledge.flip correctly during an error'
        );
        $this->assertEquals(
            [
                'name'   => 'acknowledge.flip.error',
                'target' => $this->flipService,
                'params' => ['earned_flip' => $earnedFlip, 'error' => $exception],
            ],
            $this->calledEvents[1],
            FlipUserServiceDelegator::class . ' did not trigger acknowledge.flip.error correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallAcknowledgeFlip()
    {
        $earnedFlip = new EarnedFlip();
        $this->flipService->shouldReceive('acknowledgeFlip')
            ->never();

        $this->delegator->getEventManager()->attach('acknowledge.flip', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });

        $this->assertFalse(
            $this->delegator->acknowledgeFlip($earnedFlip),
            FlipUserServiceDelegator::class . ' did not return result from event '
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            FlipUserServiceDelegator::class . ' triggered the incorrect number of events when stopping acknowledge.flip'
        );
        $this->assertEquals(
            [
                'name'   => 'acknowledge.flip',
                'target' => $this->flipService,
                'params' => ['earned_flip' => $earnedFlip],
            ],
            $this->calledEvents[0],
            FlipUserServiceDelegator::class . ' triggered acknowledge.flip incorrectly when stopping acknowledge.flip'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchTheLatestAcknowledgeFlip()
    {
        $user       = new Child();
        $earnedFlip = new EarnedFlip();
        $this->flipService->shouldReceive('fetchLatestAcknowledgeFlip')
            ->once()
            ->with($user, null)
            ->andReturn($earnedFlip);

        $this->assertSame(
            $earnedFlip,
            $this->delegator->fetchLatestAcknowledgeFlip($user),
            FlipUserServiceDelegator::class . ' did not chain result from service'
        );

        $this->assertEquals(
            2,
            count($this->calledEvents),
            FlipUserServiceDelegator::class . ' triggered the incorrect number of events when stopping acknowledge.flip'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.acknowledge.flip',
                'target' => $this->flipService,
                'params' => ['user' => $user, 'prototype' => null],
            ],
            $this->calledEvents[0],
            FlipUserServiceDelegator::class . ' triggered acknowledge.flip incorrectly when stopping acknowledge.flip'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.acknowledge.flip.post',
                'target' => $this->flipService,
                'params' => ['user' => $user, 'prototype' => null, 'flip' => $earnedFlip],
            ],
            $this->calledEvents[1],
            FlipUserServiceDelegator::class . ' triggered acknowledge.flip incorrectly when stopping acknowledge.flip'
        );
    }

    /**
     * @test
     */
    public function testItShouldReThrowExceptionOnFetchLatestAcknowledgeFlip()
    {
        $user      = new Child();
        $exception = new NotFoundException();
        $this->flipService->shouldReceive('fetchLatestAcknowledgeFlip')
            ->once()
            ->with($user, null)
            ->andThrow($exception);

        try {
            $this->delegator->fetchLatestAcknowledgeFlip($user);
            $this->fail(FlipUserServiceDelegator::class . ' did not throw the error');
        } catch (\Throwable $actualException) {
            $this->assertSame(
                $actualException,
                $exception
            );
        }

        $this->assertEquals(
            2,
            count($this->calledEvents),
            FlipUserServiceDelegator::class . ' triggered the incorrect number of events when stopping acknowledge.flip'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.acknowledge.flip',
                'target' => $this->flipService,
                'params' => ['user' => $user, 'prototype' => null],
            ],
            $this->calledEvents[0],
            FlipUserServiceDelegator::class . ' triggered acknowledge.flip incorrectly when stopping acknowledge.flip'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.acknowledge.flip.error',
                'target' => $this->flipService,
                'params' => ['user' => $user, 'prototype' => null, 'error' => $exception],
            ],
            $this->calledEvents[1],
            FlipUserServiceDelegator::class . ' triggered acknowledge.flip incorrectly when stopping acknowledge.flip'
        );
    }
}
