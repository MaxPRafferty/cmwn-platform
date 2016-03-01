<?php

namespace UserTest\Delegator;

use \PHPUnit_Framework_TestCase as TestCase;
use User\Adult;
use User\Delegator\UserServiceDelegator;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;

/**
 * Test UserServiceDelegatorTest
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
class UserServiceDelegatorTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\User\Service\UserService
     */
    protected $userService;

    /**
     * @var UserServiceDelegator
     */
    protected $delegator;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @var Adult
     */
    protected $user;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->userService = \Mockery::mock('\User\Service\UserService');
    }

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->calledEvents = [];
        $this->delegator    = new UserServiceDelegator($this->userService);
        $this->delegator->getEventManager()->clearListeners('save.user');
        $this->delegator->getEventManager()->clearListeners('fetch.user.post');
        $this->delegator->getEventManager()->clearListeners('fetch.all.users');
        $this->delegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }

    /**
     * @before
     */
    public function setUpUser()
    {
        $this->user = new Adult();
        $this->user->setUserId(md5('foobar'));
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

    public function testItShouldCallCreateUser()
    {
        $this->userService->shouldReceive('createUser')
            ->with($this->user)
            ->andReturn(true)
            ->once();


        $this->delegator->createUser($this->user);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'save.new.user',
                'target' => $this->userService,
                'params' => ['user' => $this->user]
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'save.new.user.post',
                'target' => $this->userService,
                'params' => ['user' => $this->user]
            ],
            $this->calledEvents[1]
        );
    }

    public function testItShouldCallUpdateUser()
    {
        $this->userService->shouldReceive('updateUser')
            ->with($this->user)
            ->andReturn(true)
            ->once();


        $this->delegator->updateUser($this->user);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'save.user',
                'target' => $this->userService,
                'params' => ['user' => $this->user]
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'save.user.post',
                'target' => $this->userService,
                'params' => ['user' => $this->user]
            ],
            $this->calledEvents[1]
        );
    }

    public function testItShouldNotCallCreateUserWhenEventPrevents()
    {
        $this->userService->shouldReceive('createUser')
            ->with($this->user)
            ->never();

        $this->delegator->getEventManager()->attach('save.new.user', function (Event $event) {
            $event->stopPropagation(true);
            return ['foo' => 'bar'];
        });

        $this->assertEquals(['foo' => 'bar'], $this->delegator->createUser($this->user));

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'save.new.user',
                'target' => $this->userService,
                'params' => ['user' => $this->user]
            ],
            $this->calledEvents[0]
        );
    }

    public function testItShouldNotCallUpdateUserWhenEventPrevents()
    {
        $this->userService->shouldReceive('UpdateUser')
            ->with($this->user)
            ->never();

        $this->delegator->getEventManager()->attach('save.user', function (Event $event) {
            $event->stopPropagation(true);
            return ['foo' => 'bar'];
        });

        $this->assertEquals(['foo' => 'bar'], $this->delegator->updateUser($this->user));

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'save.user',
                'target' => $this->userService,
                'params' => ['user' => $this->user]
            ],
            $this->calledEvents[0]
        );
    }

    public function testItShouldCallFetchUser()
    {
        $this->userService->shouldReceive('fetchUser')
            ->with($this->user->getUserId())
            ->andReturn($this->user)
            ->once();

        $this->assertSame(
            $this->user,
            $this->delegator->fetchUser($this->user->getUserId())
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.user',
                'target' => $this->userService,
                'params' => ['user_id' => $this->user->getUserId()]
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.user.post',
                'target' => $this->userService,
                'params' => ['user' => $this->user, 'user_id' => $this->user->getUserId()]
            ],
            $this->calledEvents[1]
        );
    }

    public function testItShouldNotCallFetchUserAndReturnEventResult()
    {
        $this->userService->shouldReceive('fetchUser')
            ->with($this->user->getUserId())
            ->andReturn($this->user)
            ->never();

        $this->delegator->getEventManager()->attach('fetch.user', function (Event $event) {
            $event->stopPropagation(true);
            return $this->user;
        });

        $this->assertSame(
            $this->user,
            $this->delegator->fetchUser($this->user->getUserId())
        );

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.user',
                'target' => $this->userService,
                'params' => ['user_id' => $this->user->getUserId()]
            ],
            $this->calledEvents[0]
        );
    }
    
    public function testItShouldCallDeleteUser()
    {
        $this->userService->shouldReceive('deleteUser')
            ->with($this->user, true)
            ->andReturn($this->user)
            ->once();

        $this->assertSame(
            $this->user,
            $this->delegator->deleteUser($this->user)
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.user',
                'target' => $this->userService,
                'params' => ['user' => $this->user, 'soft' => true]
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'delete.user.post',
                'target' => $this->userService,
                'params' => ['user' => $this->user, 'soft' => true]
            ],
            $this->calledEvents[1]
        );
    }

    public function testItShouldNotCallDeleteUserAndReturnEventResult()
    {
        $this->userService->shouldReceive('deleteUser')
            ->with($this->user, true)
            ->andReturn($this->user)
            ->never();

        $this->delegator->getEventManager()->attach('delete.user', function (Event $event) {
            $event->stopPropagation(true);
            return $this->user;
        });

        $this->assertSame(
            $this->user,
            $this->delegator->deleteUser($this->user)
        );

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.user',
                'target' => $this->userService,
                'params' => ['user' => $this->user, 'soft' => true]
            ],
            $this->calledEvents[0]
        );
    }

    public function testItShouldCallFetchAll()
    {
        $result = new \ArrayIterator([['foo' => 'bar']]);
        $this->userService->shouldReceive('fetchAll')
            ->andReturn($result)
            ->once();

        $this->assertSame(
            $result,
            $this->delegator->fetchAll()
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.users',
                'target' => $this->userService,
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null]
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.users.post',
                'target' => $this->userService,
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null, 'users' => $result]
            ],
            $this->calledEvents[1]
        );
    }

    public function testItShouldCallFetchAllWhenEventStops()
    {
        $result = new \ArrayIterator([['foo' => 'bar']]);
        $this->userService->shouldReceive('fetchAll')
            ->andReturn($result)
            ->never();

        $this->delegator->getEventManager()->attach('fetch.all.users', function (Event $event) use (&$result) {
            $event->stopPropagation(true);
            return $result;
        });

        $this->assertSame(
            $result,
            $this->delegator->fetchAll()
        );

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.users',
                'target' => $this->userService,
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null]
            ],
            $this->calledEvents[0]
        );
    }
}
