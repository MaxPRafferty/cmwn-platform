<?php

namespace UserTest\Service;

use \PHPUnit_Framework_TestCase as TestCase;
use User\Child;
use User\Delegator\UserServiceDelegator;
use User\Service\RandomNameListener;
use Zend\Db\Sql\Expression;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManager;

/**
 * Exception RandomNameListenerTest
 */
class RandomNameListenerTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * @var SharedEventManager
     */
    protected $sharedEvents;

    /**
     * @var RandomNameListener
     */
    protected $listener;

    /**
     * @var EventManager
     */
    protected $events;

    /**
     * @before
     */
    public function setUpGateWay()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter */
        $adapter = \Mockery::mock('\Zend\Db\Adapter\Adapter');
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock('\Zend\Db\TableGateway\TableGateway');
        $this->tableGateway->shouldReceive('getTable')->andReturn('users')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @before
     */
    public function setUpListener()
    {
        $this->listener = new RandomNameListener($this->tableGateway);
    }

    /**
     * @before
     */
    public function setUpSharedEvents()
    {
        $this->sharedEvents = new SharedEventManager();
        $this->sharedEvents->clearListeners(UserServiceDelegator::class);
        $this->listener->attachShared($this->sharedEvents);
    }

    /**
     * @before
     */
    public function setUpEventManager()
    {
        $this->events = new EventManager();
        $this->events->addIdentifiers(UserServiceDelegator::class);
    }

    public function testItShouldReserveRandomNameLessThanAThousand()
    {
        $this->markTestSkipped('Cant clear shared listeners at this time');
        $user     = new Child();
        $userName = $user->getGeneratedName();
        $beforeRunName = $userName->userName;

        $return = [
            [
                'name'     => $userName->left,
                'position' => 'LEFT',
                'count'    => 5,
            ],
            [
                'name'     => $userName->right,
                'position' => 'RIGHT',
                'count'    => 5,
            ]
        ];

        $this->tableGateway->shouldReceive('select')
            ->once()
            ->with(['name' => [$userName->left, $userName->right]])
            ->andReturn($return);

        $this->tableGateway->shouldReceive('update')
            ->once()
            ->with(
                ['count' => new Expression('count + 1')],
                ['name' => [$userName->left, $userName->right]]
            );

        $this->events->trigger('save.new.user', new \stdClass(), ['user' => $user]);

        $this->assertNotEquals(
            $beforeRunName,
            $userName->userName,
            'User name has not changed'
        );
    }
}
