<?php

namespace UserTest\Service;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use User\Adult;
use User\Child;
use User\Service\RandomNameListener;
use User\UserInterface;
use Zend\Db\Sql\Expression;
use Zend\EventManager\Event;

/**
 * Test RandomNameListenerTest
 *
 * @group User
 * @group Service
 * @group RandomNameService
 */
class RandomNameListenerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|\Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * @var RandomNameListener
     */
    protected $listener;

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
        $this->markTestSkipped('Fix the StaticNameService');
        $this->listener = new RandomNameListener($this->tableGateway);
    }

    /**
     * @param UserInterface $user
     *
     * @return Event
     */
    public function getEvent(UserInterface $user)
    {
        return new Event(
            'save.new.user',
            new \stdClass(),
            ['user' => $user]
        );
    }

    /**
     * @test
     */
    public function testItShouldReserveRandomNameLessThanAThousand()
    {
        $user = new Child();
        $user->getUserName();
        $userName      = $user->getGeneratedName();
        $beforeRunName = $userName->userName;

        $return = new \ArrayObject([
            [
                'name'     => $userName->left,
                'position' => 'LEFT',
                'count'    => 5,
            ],
            [
                'name'     => $userName->right,
                'position' => 'RIGHT',
                'count'    => 5,
            ],
        ]);

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

        $event = $this->getEvent($user);
        $this->assertEmpty($this->listener->reserveRandomName($event));
        $this->assertFalse($event->propagationIsStopped(), 'Listener must not stop propagation');

        $this->assertNotEquals(
            $beforeRunName,
            $userName->userName,
            'User name has not changed'
        );

        $this->assertRegExp('/[a-z]+\-[a-z]+\d{3}/', $userName->userName, 'Number was not appended to user name');
    }

    /**
     * @test
     */
    public function testItShouldReserveRandomNameMoreThanAThousand()
    {
        $user = new Child();
        $user->getUserName();
        $userName      = $user->getGeneratedName();
        $beforeRunName = $userName->userName;

        $return = new \ArrayObject([
            [
                'name'     => $userName->left,
                'position' => 'LEFT',
                'count'    => 500,
            ],
            [
                'name'     => $userName->right,
                'position' => 'RIGHT',
                'count'    => 500,
            ],
        ]);

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

        $event = $this->getEvent($user);
        $this->assertEmpty($this->listener->reserveRandomName($event));
        $this->assertFalse($event->propagationIsStopped(), 'Listener must not stop propagation');

        $this->assertNotEquals(
            $beforeRunName,
            $userName->userName,
            'User name has not changed'
        );

        $this->assertRegExp('/[a-z]+\-[a-z]+\d{4}/', $userName->userName, 'Number was not appended to user name');
    }

    /**
     * @test
     */
    public function testItShouldDoNothingWhenPassedAdult()
    {
        $this->tableGateway->shouldNotReceive('select');
        $this->tableGateway->shouldNotReceive('update');

        $user = new Adult();

        $event = $this->getEvent($user);
        $this->assertEmpty($this->listener->reserveRandomName($event));
        $this->assertFalse($event->propagationIsStopped(), 'Listener must not stop propagation');
    }

    /**
     * @test
     */
    public function testItShouldDoNothingWhenNoNamesReturned()
    {
        $user = new Child();
        $user->getUserName();
        $userName      = $user->getGeneratedName();
        $beforeRunName = $userName->userName;

        $return = new \ArrayObject();

        $this->tableGateway->shouldReceive('select')
            ->once()
            ->with(['name' => [$userName->left, $userName->right]])
            ->andReturn($return);

        $this->tableGateway->shouldNotReceive('update');

        $event = $this->getEvent($user);
        $this->assertEmpty($this->listener->reserveRandomName($event));
        $this->assertFalse($event->propagationIsStopped(), 'Listener must not stop propagation');

        $this->assertEquals(
            $beforeRunName,
            $userName->userName,
            'Username must not be adjusted when no names returned from db'
        );
    }
}
