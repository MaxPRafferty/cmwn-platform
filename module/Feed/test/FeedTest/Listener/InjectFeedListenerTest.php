<?php

namespace FeedTest\Listener;

use Feed\Listener\InjectFeedListener;
use Feed\Service\FeedServiceInterface;
use Feed\Service\FeedUserServiceInterface;
use Flip\Service\FlipServiceInterface;
use Friend\FriendInterface;
use Friend\Service\FriendServiceInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use Skribble\Skribble;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;

class InjectFeedListenerTest extends TestCase
{
    /**
     * @var FeedServiceInterface | \Mockery\MockInterface
     */
    protected $feedService;

    /**
     * @var \Feed\Service\FeedUserServiceInterface | \Mockery\MockInterface
     */
    protected $feedUserService;

    /**
     * @var \Friend\Service\FriendServiceInterface | \Mockery\MockInterface
     */
    protected $friendService;

    /**
     * @var InjectFeedListener
     */
    protected $listener;

    /**
     * @before
     */
    public function setUpServices()
    {
        $this->feedService = \Mockery::mock(FeedServiceInterface::class);
        $this->feedUserService = \Mockery::mock(FeedUserServiceInterface::class);
        $this->friendService = \Mockery::mock(FriendServiceInterface::class);
    }

    /**
     * @before
     */
    public function setUpListener()
    {
        $this->listener = new InjectFeedListener($this->feedService, $this->feedUserService, $this->friendService);
    }

    /**
     * @test
     */
    public function testItShouldCallInjectFlipFeed()
    {
        $event = new Event();
        $event->setName('attach.flip.post');
        $event->setTarget(FlipServiceInterface::class);
        $event->setParam('flip', 'polar-bear');

        $eventManager = new EventManager();
        $eventManager->attach('attach.flip.post', [$this->listener, 'injectFlipFeed']);

        $this->feedService->shouldReceive('createFeed')
            ->once();
        $this->feedUserService->shouldReceive('attachFeedForUser')
            ->once();
        $response = $eventManager->triggerEvent($event);

        $this->assertFalse($response->stopped());
    }

    /**
     * @test
     */
    public function testItShouldCallInjectFriendFeed()
    {
        $event = new Event();
        $event->setName('attach.friend.post');
        $event->setTarget(FriendServiceInterface::class);
        $event->setParam('friend', 'math_student');
        $event->setParam('user', 'english_student');

        $eventManager = new EventManager();
        $eventManager->attach('attach.friend.post', [$this->listener, 'injectFriendFeed']);

        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->andReturn(FriendInterface::FRIEND)
            ->once();
        $this->feedService->shouldReceive('createFeed')
            ->once();
        $this->feedUserService->shouldReceive('attachFeedForUser')
            ->twice();
        $response = $eventManager->triggerEvent($event);

        $this->assertFalse($response->stopped());
    }

    /**
     * @test
     */
    public function testItShouldNotInjectFriendFeedIfParamsAreNull()
    {
        $event = new Event();
        $event->setName('attach.friend.post');
        $event->setTarget(FriendServiceInterface::class);

        $eventManager = new EventManager();
        $eventManager->attach('attach.friend.post', [$this->listener, 'injectFriendFeed']);

        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->andReturn(FriendInterface::FRIEND)
            ->never();
        $this->feedService->shouldReceive('createFeed')
            ->never();
        $this->feedUserService->shouldReceive('attachFeedForUser')
            ->never();
        $response = $eventManager->triggerEvent($event);

        $this->assertFalse($response->stopped());
    }

    /**
     * @test
     */
    public function testItShouldNotInjectFriendFeedIfNotFriends()
    {
        $event = new Event();
        $event->setName('attach.friend.post');
        $event->setTarget(FriendServiceInterface::class);

        $eventManager = new EventManager();
        $eventManager->attach('attach.friend.post', [$this->listener, 'injectFriendFeed']);

        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->andReturn(FriendInterface::PENDING)
            ->never();
        $this->feedService->shouldReceive('createFeed')
            ->never();
        $this->feedUserService->shouldReceive('attachFeedForUser')
            ->never();
        $response = $eventManager->triggerEvent($event);

        $this->assertFalse($response->stopped());
    }
    
    /**
     * @test
     */
    public function testItShouldInjectSkribbleFeedForCompletedSkribbles()
    {
        $skribble = new Skribble([
            'skribble_id' => 'foo-bar',
            'status' => 'COMPLETE',
            'created_by' => 'english_student',
            'friend_to' => 'math_student'
        ]);
        $event = new Event();
        $event->setName('create.skribble.post');
        $event->setTarget(FlipServiceInterface::class);
        $event->setParam('skribble', $skribble);

        $eventManager = new EventManager();
        $eventManager->attach('create.skribble.post', [$this->listener, 'injectSkribbleFeed']);

        $this->feedService->shouldReceive('createFeed')
            ->once();
        $this->feedUserService->shouldReceive('attachFeedForUser')
            ->once();
        $response = $eventManager->triggerEvent($event);

        $this->assertFalse($response->stopped());
    }

    /**
     * @test
     */
    public function testItShouldNotInjectSkribbleFeedForIncompleteSkribbles()
    {
        $skribble = new Skribble([
            'skribble_id' => 'foo-bar',
            'status' => 'NOT_COMPLETE',
            'created_by' => 'english_student',
            'friend_to' => 'math_student'
        ]);
        $event = new Event();
        $event->setName('create.skribble.post');
        $event->setTarget(FlipServiceInterface::class);
        $event->setParam('skribble', $skribble);

        $eventManager = new EventManager();
        $eventManager->attach('create.skribble.post', [$this->listener, 'injectSkribbleFeed']);

        $this->feedService->shouldReceive('createFeed')
            ->never();
        $this->feedUserService->shouldReceive('attachFeedForUser')
            ->never();
        $response = $eventManager->triggerEvent($event);

        $this->assertFalse($response->stopped());
    }

    /**
     * @test
     */
    public function testItShouldInjectSkribbleForUpdatedCompleteSkribbles()
    {
        $skribble = new Skribble([
            'skribble_id' => 'foo-bar',
            'status' => 'COMPLETE',
            'created_by' => 'english_student',
            'friend_to' => 'math_student'
        ]);
        $event = new Event();
        $event->setName('update.skribble.post');
        $event->setTarget(FlipServiceInterface::class);
        $event->setParam('skribble', $skribble);

        $eventManager = new EventManager();
        $eventManager->attach('update.skribble.post', [$this->listener, 'injectSkribbleFeed']);

        $this->feedService->shouldReceive('createFeed')
            ->once();
        $this->feedUserService->shouldReceive('attachFeedForUser')
            ->once();
        $response = $eventManager->triggerEvent($event);

        $this->assertFalse($response->stopped());
    }

    /**
     * @test
     */
    public function testItShouldNotInjectFeedForUpdatedIncompleteSkribbles()
    {
        $skribble = new Skribble([
            'skribble_id' => 'foo-bar',
            'status' => 'NOT_COMPLETE',
            'created_by' => 'english_student',
            'friend_to' => 'math_student'
        ]);
        $event = new Event();
        $event->setName('update.skribble.post');
        $event->setTarget(FlipServiceInterface::class);
        $event->setParam('skribble', $skribble);

        $eventManager = new EventManager();
        $eventManager->attach('update.skribble.post', [$this->listener, 'injectSkribbleFeed']);

        $this->feedService->shouldReceive('createFeed')
            ->never();
        $this->feedUserService->shouldReceive('attachFeedForUser')
            ->never();
        $response = $eventManager->triggerEvent($event);

        $this->assertFalse($response->stopped());
    }
}
