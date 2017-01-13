<?php

namespace FeedTest\Delegator;

use Application\Exception\NotFoundException;
use Feed\Delegator\FeedUserDelegator;
use Feed\Service\FeedUserService;
use Feed\UserFeed;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\ResultSet\ResultSet;
use Zend\EventManager\Event;

/**
 * Class FeedUserDelegatorTest
 * @package FeedTest\Delegator
 * @SuppressWarnings(PHPMD)
 */
class FeedUserDelegatorTest extends TestCase
{
    /**
     * @var FeedUserService | \Mockery\MockInterface
     */
    protected $service;
    
    /**
     * @var FeedUserDelegator
     */
    protected $delegator;
    
    /**
     * @var array
     */
    protected $calledEvents;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->service = \Mockery::mock(FeedUserService::class);
    }
    
    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->calledEvents = [];
        $this->delegator = new FeedUserDelegator($this->service);
        $this->delegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }

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
     * @test
     */
    public function testItShouldCallFetchAllFeedForUser()
    {
        $result = new ResultSet([]);
        $this->service->shouldReceive('fetchAllFeedForUser')
            ->with('english_student', null, null)
            ->andReturn($result)
            ->once();
        $this->delegator->fetchAllFeedForUser('english_student');
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'fetch.all.user.feed',
                'target' => $this->service,
                'params' => ['user' => 'english_student', 'where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name' => 'fetch.all.user.feed.post',
                'target' => $this->service,
                'params' => [
                    'user' => 'english_student',
                    'where' => null,
                    'prototype' => null,
                    'user_feeds' => $result
                ],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchAllFeedForUserWhenEventStops()
    {
        $this->service->shouldReceive('fetchAllFeedForUser')
            ->never();
        $this->delegator->getEventManager()
            ->attach('fetch.all.user.feed', function (Event $event) {
                $event->stopPropagation(true);
            });

        $this->delegator->fetchAllFeedForUser('english_student');

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'fetch.all.user.feed',
                'target' => $this->service,
                'params' => ['user' => 'english_student', 'where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchFeedForUser()
    {
        $userFeed = new UserFeed(['feed_id' => 'es_friend_feed']);
        $this->service->shouldReceive('fetchFeedForUser')
            ->with('english_student', 'es_friend_feed', null, null)
            ->andReturn($userFeed)
            ->once();
        $this->delegator->fetchFeedForUser('english_student', 'es_friend_feed');

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'fetch.user.feed',
                'target' => $this->service,
                'params' => [
                    'user' => 'english_student',
                    'feed_id' => 'es_friend_feed',
                    'where' => null,
                    'prototype' => null
                ],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name' => 'fetch.user.feed.post',
                'target' => $this->service,
                'params' => [
                    'user' => 'english_student',
                    'feed_id' => 'es_friend_feed',
                    'where' => null,
                    'prototype' => null,
                    'user_feed' => $userFeed
                ],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchFeedForUserWhenEventStops()
    {
        $this->service->shouldReceive('fetchFeedForUser')
            ->never();

        $this->delegator->getEventManager()
            ->attach('fetch.user.feed', function (Event $event) {
                $event->stopPropagation(true);
            });
        $this->delegator->fetchFeedForUser('english_student', 'es_friend_feed');

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'fetch.user.feed',
                'target' => $this->service,
                'params' => [
                    'user' => 'english_student',
                    'feed_id' => 'es_friend_feed',
                    'where' => null,
                    'prototype' => null
                ],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenFeedNotFound()
    {
        $exception = new NotFoundException();
        $this->service->shouldReceive('fetchFeedForUser')
            ->with('english_student', 'es_friend_feed', null, null)
            ->andThrow($exception)
            ->once();

        $this->setExpectedException(NotFoundException::class);
        $this->delegator->fetchFeedForUser('english_student', 'es_friend_feed');

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'fetch.user.feed',
                'target' => $this->service,
                'params' => [
                    'user' => 'english_student',
                    'feed_id' => 'es_friend_feed',
                    'where' => null,
                    'prototype' => null
                ],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name' => 'fetch.user.feed.post',
                'target' => $this->service,
                'params' => [
                    'user' => 'english_student',
                    'feed_id' => 'es_friend_feed',
                    'where' => null,
                    'prototype' => null,
                    'exception' => $exception
                ],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallAttachFeedForUser()
    {
        $userFeed = new UserFeed(['feed_id' => 'es_friend_feed']);

        $this->service->shouldReceive('attachFeedForUser')
            ->with('english_student', $userFeed)
            ->andReturn(true);

        $this->delegator->attachFeedForUser('english_student', $userFeed);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'attach.user.feed',
                'target' => $this->service,
                'params' => [
                    'user' => 'english_student',
                    'user_feed' => $userFeed,
                ],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name' => 'attach.user.feed.post',
                'target' => $this->service,
                'params' => [
                    'user' => 'english_student',
                    'user_feed' => $userFeed,
                ],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallAttachAllFeedForUserWhenEventStops()
    {
        $userFeed = new UserFeed(['feed_id' => 'es_friend_feed']);
        $this->service->shouldReceive('attach.user.feed')
            ->never();

        $this->delegator->getEventManager()
            ->attach('attach.user.feed', function (Event $event) {
                $event->stopPropagation(true);
            });

        $this->delegator->attachFeedForUser('english_student', $userFeed);

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'attach.user.feed',
                'target' => $this->service,
                'params' => [
                    'user' => 'english_student',
                    'user_feed' => $userFeed,
                ],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldUpdateFeedForUser()
    {
        $userFeed = new UserFeed(['feed_id' => 'es_friend_feed']);

        $this->service->shouldReceive('updateFeedForUser')
            ->with('english_student', $userFeed)
            ->andReturn(true);

        $this->delegator->updateFeedForUser('english_student', $userFeed);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'update.user.feed',
                'target' => $this->service,
                'params' => [
                    'user' => 'english_student',
                    'user_feed' => $userFeed,
                ],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name' => 'update.user.feed.post',
                'target' => $this->service,
                'params' => [
                    'user' => 'english_student',
                    'user_feed' => $userFeed,
                ],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallUpdateFeedForUserWhenEventStops()
    {
        $userFeed = new UserFeed(['feed_id' => 'es_friend_feed']);
        $this->service->shouldReceive('update.user.feed')
            ->never();

        $this->delegator->getEventManager()
            ->attach('update.user.feed', function (Event $event) {
                $event->stopPropagation(true);
            });

        $this->delegator->updateFeedForUser('english_student', $userFeed);

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'update.user.feed',
                'target' => $this->service,
                'params' => [
                    'user' => 'english_student',
                    'user_feed' => $userFeed,
                ],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallDeleteFeedForUser()
    {
        $userFeed = new UserFeed(['feed_id' => 'es_friend_feed']);

        $this->service->shouldReceive('deleteFeedForUser')
            ->with('english_student', $userFeed)
            ->andReturn(true);

        $this->delegator->deleteFeedForUser('english_student', $userFeed);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'delete.user.feed',
                'target' => $this->service,
                'params' => [
                    'user' => 'english_student',
                    'user_feed' => $userFeed,
                ],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name' => 'delete.user.feed.post',
                'target' => $this->service,
                'params' => [
                    'user' => 'english_student',
                    'user_feed' => $userFeed,
                ],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallDeleteFeedForUserWhenEventStops()
    {
        $userFeed = new UserFeed(['feed_id' => 'es_friend_feed']);
        $this->service->shouldReceive('delete.user.feed')
            ->never();

        $this->delegator->getEventManager()
            ->attach('delete.user.feed', function (Event $event) {
                $event->stopPropagation(true);
            });

        $this->delegator->deleteFeedForUser('english_student', $userFeed);

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'delete.user.feed',
                'target' => $this->service,
                'params' => [
                    'user' => 'english_student',
                    'user_feed' => $userFeed,
                ],
            ],
            $this->calledEvents[0]
        );
    }
}
