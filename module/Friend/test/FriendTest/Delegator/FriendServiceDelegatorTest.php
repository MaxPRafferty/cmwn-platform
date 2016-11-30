<?php

namespace FriendTest\Delegator;

use Application\Utils\ServiceTrait;
use Friend\Service\FriendServiceInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use Friend\Delegator\FriendServiceDelegator;
use User\UserInterface;
use Zend\EventManager\Event;
use User\Child;

/**
 * Test FriendServiceDelegatorTest
 *
 * @group Friend
 * @group Service
 * @group FriendService
 * @group Delegator
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FriendServiceDelegatorTest extends TestCase
{
    use ServiceTrait;
    /**
     * @var \Mockery\MockInterface|\Friend\Service\FriendService
     */
    protected $friendService;

    /**
     * @var FriendServiceDelegator
     */
    protected $delegator;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var UserInterface
     */
    protected $friend;

    /**
     * @var array
     */
    protected $calledEvents = [];

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
     * @before
     */
    public function setUpFriendService()
    {
        $this->friendService = \Mockery::mock('\Friend\Service\FriendService');
    }

    /**
     * @before
     */
    public function setUpUser()
    {
        $this->user = new Child(['user_id' => 'user']);
    }

    /**
     * @before
     */
    public function setUpFriend()
    {
        $this->friend = new Child(['user_id' => 'friend_user']);
    }

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->delegator = new FriendServiceDelegator($this->friendService);
        $this->delegator->getEventManager()->clearListeners('*');
        if ($this->delegator->getEventManager()->getSharedManager()) {
            $this->delegator->getEventManager()
                ->getSharedManager()
                ->clearListeners(FriendServiceInterface::class, 'attach.friend.post');
            $this->delegator->getEventManager()
                ->getSharedManager()
                ->clearListeners(FriendServiceDelegator::class, 'attach.friend.post');
        }
        $this->delegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }

    /**
     * @test
     */
    public function testItShouldCallFetchFriendsForUser()
    {
        $this->friendService->shouldReceive('fetchFriendsForUser')
            ->once();
        $this->delegator->fetchFriendsForUser($this->user);
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.friends',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0],
            'Pre event for fetchFriendsForUser Is incorrect'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.friends.post',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'where' => null, 'prototype' => null, 'result' => null],
            ],
            $this->calledEvents[1],
            'Post event for fetchFriendsForUser Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallAttachFriendToUser()
    {
        $this->friendService->shouldReceive('attachFriendToUser')
            ->once();

        $this->delegator->attachFriendToUser($this->user, $this->friend);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'attach.friend',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend],
            ],
            $this->calledEvents[0],
            'Pre event for attachFriendToUser Is incorrect'
        );

        $this->assertEquals(
            [
                'name'   => 'attach.friend.post',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend],
            ],
            $this->calledEvents[1],
            'Post event for attachFriendToUser Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallDetachFriendFromUser()
    {
        $this->friendService->shouldReceive('detachFriendFromUser')
            ->once();
        $this->delegator->detachFriendFromUser($this->user, $this->friend);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'detach.friend',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend],
            ],
            $this->calledEvents[0],
            'Pre event for detachFriendFromUser Is incorrect'
        );

        $this->assertEquals(
            [
                'name'   => 'detach.friend.post',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend],
            ],
            $this->calledEvents[1],
            'Post event for detachFriendFromUser Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchFriendForUser()
    {
        $this->friendService->shouldReceive('fetchFriendForUser')
            ->once();
        $this->delegator->fetchFriendForUser($this->user, $this->friend);
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.friend',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend, 'prototype' => null],
            ],
            $this->calledEvents[0],
            'Pre event for fetchFriendForUser Is incorrect'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.friend.post',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend, 'prototype' => null],
            ],
            $this->calledEvents[1],
            'Post event for fetchFriendForUser Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchFriendStatusForUser()
    {
        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->once();
        $this->delegator->fetchFriendStatusForUser($this->user, $this->friend);
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.friend.status',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend],
            ],
            $this->calledEvents[0],
            'Pre event for fetchFriendStatusForUser Is incorrect'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.friend.status.post',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend, 'status' => null],
            ],
            $this->calledEvents[1],
            'Post event for fetchFriendStatusForUser Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchFriendsForUserWhenEventStops()
    {
        $this->friendService->shouldReceive('fetchFriendsForUser')
            ->never();
        $this->delegator->getEventManager()->attach('fetch.all.friends', function (Event $event) {
            $event->stopPropagation(true);
        });
        $this->delegator->fetchFriendsForUser($this->user);
        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.friends',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0],
            'Pre event for fetchFriendsForUser Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallAttachFriendToUserWhenEventStops()
    {
        $this->friendService->shouldReceive('attachFriendToUser')
            ->never();

        $this->delegator->getEventManager()->attach('attach.friend', function (Event $event) {
            $event->stopPropagation(true);
        });
        $this->delegator->attachFriendToUser($this->user, $this->friend);

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'attach.friend',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend],
            ],
            $this->calledEvents[0],
            'Pre event for attachFriendToUser Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallDetachFriendFromUserWhenEventStops()
    {
        $this->friendService->shouldReceive('detachFriendFromUser')
            ->never();

        $this->delegator->getEventManager()->attach('detach.friend', function (Event $event) {
            $event->stopPropagation(true);
        });
        $this->delegator->detachFriendFromUser($this->user, $this->friend);

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'detach.friend',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend],
            ],
            $this->calledEvents[0],
            'Pre event for detachFriendFromUser Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchFriendForUserWhenEventStops()
    {
        $this->friendService->shouldReceive('fetchFriendForUser')
            ->never();

        $this->delegator->getEventManager()->attach('fetch.friend', function (Event $event) {
            $event->stopPropagation(true);
        });
        $this->delegator->fetchFriendForUser($this->user, $this->friend);
        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.friend',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend, 'prototype' => null],
            ],
            $this->calledEvents[0],
            'Pre event for fetchFriendForUser Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchFriendStatusForUserWhenEventStops()
    {
        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->never();

        $this->delegator->getEventManager()->attach('fetch.friend.status', function (Event $event) {
            $event->stopPropagation(true);
        });
        $this->delegator->fetchFriendStatusForUser($this->user, $this->friend);
        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.friend.status',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend],
            ],
            $this->calledEvents[0],
            'Pre event for fetchFriendStatusForUser Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchFriendsForUserPostWhenException()
    {
        $exception = new \Exception();
        $this->friendService->shouldReceive('fetchFriendsForUser')
            ->andThrow($exception)
            ->once();
        try {
            $this->delegator->fetchFriendsForUser($this->user);
            $this->fail('Exception was not re thrown');
        } catch (\Exception $test) {
            // noop
        }
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.friends',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0],
            'Pre event for fetchFriendsForUser Is incorrect'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.friends.error',
                'target' => $this->friendService,
                'params' => [
                    'user'      => $this->user,
                    'where'     => null,
                    'prototype' => null,
                    'exception' => $exception,
                ],
            ],
            $this->calledEvents[1],
            'Post event for fetchFriendsForUser Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallAttachFriendToUserWhenException()
    {
        $exception = new \Exception();
        $this->friendService->shouldReceive('attachFriendToUser')
            ->andThrow($exception)
            ->once();
        try {
            $this->delegator->attachFriendToUser($this->user, $this->friend);
            $this->fail("Exception was not rethrown");
        } catch (\Exception $test) {
            //noop
        }

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'attach.friend',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend],
            ],
            $this->calledEvents[0],
            'Pre event for attachFriendToUser Is incorrect'
        );

        $this->assertEquals(
            [
                'name'   => 'attach.friend.error',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend, 'exception' => $exception],
            ],
            $this->calledEvents[1],
            'Post event for attachFriendToUser Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallDetachFriendFromUserWhenException()
    {
        $exception = new \Exception();
        $this->friendService->shouldReceive('detachFriendFromUser')
            ->andThrow($exception)
            ->once();
        try {
            $this->delegator->detachFriendFromUser($this->user, $this->friend);
            $this->fail("Exception was not rethrown");
        } catch (\Exception $test) {
            //noop
        }

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'detach.friend',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend],
            ],
            $this->calledEvents[0],
            'Pre event for detachFriendFromUser Is incorrect'
        );

        $this->assertEquals(
            [
                'name'   => 'detach.friend.error',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend, 'exception' => $exception],
            ],
            $this->calledEvents[1],
            'Post event for detachFriendFromUser Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchFriendForUserWhenException()
    {
        $exception = new \Exception();
        $this->friendService->shouldReceive('fetchFriendForUser')
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->fetchFriendForUser($this->user, $this->friend);
            $this->fail("Exception was not rethrown");
        } catch (\Exception $test) {
            //noop
        }
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.friend',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend, 'prototype' => null],
            ],
            $this->calledEvents[0],
            'Pre event for fetchFriendForUser Is incorrect'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.friend.error',
                'target' => $this->friendService,
                'params' => [
                    'user'      => $this->user,
                    'friend'    => $this->friend,
                    'prototype' => null,
                    'exception' => $exception,
                ],
            ],
            $this->calledEvents[1],
            'Post event for fetchFriendForUser Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchFriendStatusForUserWhenException()
    {
        $exception = new \Exception();
        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->fetchFriendStatusForUser($this->user, $this->friend);
            $this->fail("Exception was not rethrown");
        } catch (\Exception $test) {
            //noop
        }

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.friend.status',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend],
            ],
            $this->calledEvents[0],
            'Pre event for fetchFriendStatusForUser Is incorrect'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.friend.status.error',
                'target' => $this->friendService,
                'params' => ['user' => $this->user, 'friend' => $this->friend, 'exception' => $exception],
            ],
            $this->calledEvents[1],
            'Post event for fetchFriendStatusForUser Is incorrect'
        );
    }
}
