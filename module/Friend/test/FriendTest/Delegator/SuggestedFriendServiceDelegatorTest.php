<?php

namespace FriendTest\Delegator;

use Application\Utils\ServiceTrait;
use Friend\Delegator\SuggestedFriendServiceDelegator;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Child;
use User\UserInterface;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;

/**
 * Test SuggestedFriendServiceDelegatorTest
 * @group SuggestedFriend
 * @group Service
 * @group SuggestedFriendService
 * @group Delegator
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SuggestedFriendServiceDelegatorTest extends TestCase
{
    use ServiceTrait;

    /**
     * @var \Mockery\MockInterface|\Friend\Service\SuggestedFriendService
     */
    protected $suggestedFriendService;

    /**
     * @var SuggestedFriendServiceDelegator
     */
    protected $delegator;

    /**
     * @var UserInterface
     */
    protected $user;

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
            'name' => $event->getName(),
            'target' => $event->getTarget(),
            'params' => $event->getParams()
        ];
    }

    /**
     * @before
     */
    public function setUpSuggestedFriendService()
    {
        $this->suggestedFriendService = \Mockery::mock('\Friend\Service\SuggestedFriendService');
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
    public function setUpDelegator()
    {
        $this->delegator = new SuggestedFriendServiceDelegator($this->suggestedFriendService);
        $this->delegator->getEventManager()->clearListeners('fetch.suggested.friends');
        $this->delegator->getEventManager()->clearListeners('fetch.suggested.friends.post');
        $this->delegator->getEventManager()->clearListeners('fetch.suggested.friends.error');
        $this->delegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }

    /**
     * @test
     */
    public function testItShouldCallFetchFriendsForUser()
    {
        $where = new Where();
        $this->suggestedFriendService->shouldReceive('fetchSuggestedFriends')
            ->once();
        $this->delegator->fetchSuggestedFriends($this->user);
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'fetch.suggested.friends',
                'target' => $this->suggestedFriendService,
                'params' => ['user' => $this->user, 'where' => $where, 'prototype' => null]
            ],
            $this->calledEvents[0],
            'Pre event for fetchFriendsForUser Is incorrect'
        );

        $this->assertEquals(
            [
                'name' => 'fetch.suggested.friends.post',
                'target' => $this->suggestedFriendService,
                'params' => ['user' => $this->user, 'where' => $where, 'prototype' => null]
            ],
            $this->calledEvents[1],
            'Post event for fetchFriendsForUser Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchFriendsForUserWhenEventStops()
    {
        $where = new Where();
        $this->suggestedFriendService->shouldReceive('fetchSuggestedFriends')
            ->never();
        $this->delegator->getEventManager()->attach('fetch.suggested.friends', function (Event $event) {
                 $event->stopPropagation(true);
        });
        $this->delegator->fetchSuggestedFriends($this->user);
        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'fetch.suggested.friends',
                'target' => $this->suggestedFriendService,
                'params' => ['user' => $this->user, 'where' => $where, 'prototype' => null]
            ],
            $this->calledEvents[0],
            'Pre event for fetchFriendsForUser Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchFriendsForUserWhenException()
    {
        $exception = new \Exception();
        $where = new Where();
        $this->suggestedFriendService->shouldReceive('fetchSuggestedFriends')
            ->andThrow($exception)
            ->once();
        try {
            $this->delegator->fetchSuggestedFriends($this->user);
            $this->fail("Exception was not rethrown");
        } catch (\Exception $test) {
            //noop
        }
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'fetch.suggested.friends',
                'target' => $this->suggestedFriendService,
                'params' => ['user' => $this->user, 'where' => $where, 'prototype' => null]
            ],
            $this->calledEvents[0],
            'Pre event for fetchFriendsForUser Is incorrect'
        );

        $this->assertEquals(
            [
                'name' => 'fetch.suggested.friends.error',
                'target' => $this->suggestedFriendService,
                'params' => ['user' => $this->user, 'where' => $where, 'prototype' => null, 'exception' => $exception]
            ],
            $this->calledEvents[1],
            'Post event for fetchFriendsForUser Is incorrect'
        );
    }
}
