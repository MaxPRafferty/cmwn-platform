<?php

namespace RestoreDbTest\Delegator;

use RestoreDb\Delegator\RestoreDbDelegator;
use RestoreDb\Service\RestoreDbService;
use RestoreDb\Service\RestoreDbServiceInterface;
use Security\Authentication\AuthenticationService;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Zend\EventManager\Event;

/**
 * Class RestoreDbDelegatorTest
 * @package RestoreDb\test\Delegator
 */
class RestoreDbDelegatorTest extends \PHPUnit_Framework_TestCase implements AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;

    /**
     * @var \Mockery\MockInterface | \RestoreDb\Service\RestoreDbService
     */
    protected $service;

    /**
     * @var RestoreDbDelegator
     */
    protected $delegator;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @before
     */
    public function setUpService()
    {
        $this->markTestSkipped('This is a unit test it should not need a database');
        $this->service = \Mockery::mock('\RestoreDb\Service\RestoreDbService');
    }

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->delegator = new RestoreDbDelegator($this->service);
        $this->delegator->getEventManager()->clearListeners('restore.db.state');
        $this->delegator->getEventManager()->clearListeners('restore.db.state.post');
        if ($this->delegator->getEventManager()->getSharedManager()) {
            $this->delegator->getEventManager()->getSharedManager()->clearListeners(RestoreDbServiceInterface::class);
        }
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
    public function testItShouldRunDbStateRestorer()
    {
        $this->service->shouldReceive('runDbStateRestorer')->once();
        $this->delegator->runDbStateRestorer();
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'restore.db.state',
                'target' => $this->service,
                'params' => []
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name' => 'restore.db.state.post',
                'target' => $this->service,
                'params' => []
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotRunDbStateRestorerWhenEventStops()
    {
        $this->service->shouldReceive('runDbStateRestorer')->never();
        $this->delegator->getEventManager()->attach('restore.db.state', function (Event $event) {
            $event->stopPropagation(true);
        });

        $this->delegator->runDbStateRestorer();

        $this->assertEquals(1, count($this->calledEvents));

        $this->assertEquals(
            [
                'name' => 'restore.db.state',
                'target' => $this->service,
                'params' => []
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotRunDbStateRestorerOnException()
    {
        $this->service->shouldReceive('runDbStateRestorer')
            ->andThrow(\Exception::class);
        try {
            $this->delegator->runDbStateRestorer();
        } catch (\Exception $e) {
            $this->assertEquals(2, count($this->calledEvents));
            $this->assertEquals(
                [
                    'name' => 'restore.db.state',
                    'target' => $this->service,
                    'params' => []
                ],
                $this->calledEvents[0]
            );

            $this->assertEquals(
                [
                    'name' => 'restore.db.state.error',
                    'target' => $this->service,
                    'params' => ['exception' => new \Exception()]
                ],
                $this->calledEvents[1]
            );
        }
    }
}
