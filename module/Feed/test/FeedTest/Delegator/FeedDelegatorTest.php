<?php

namespace FeedTest\Delegator;

use Application\Exception\NotFoundException;
use Feed\Delegator\FeedDelegator;
use Feed\Feed;
use Feed\Service\FeedService;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\Event;

/**
 * Class FeedDelegatorTest
 * @package FeedTest\Delegator
 * @SuppressWarnings(PHPMD)
 */
class FeedDelegatorTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface | \Feed\Service\FeedService
     */
    protected $feedService;

    /**
     * @var FeedDelegator
     */
    protected $delegator;

    /**
     * @var array
     */
    protected $calledEvents;

    /**
     * @before
     */
    public function setUpFeedService()
    {
        $this->feedService = \Mockery::mock(FeedService::class);
    }

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->calledEvents = [];
        $this->delegator = new FeedDelegator($this->feedService);
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
    public function testItShouldCallFetchAll()
    {
        $result = new \ArrayIterator([]);

        $this->feedService
            ->shouldReceive('fetchAll')
            ->andReturn($result);
        $this->delegator->fetchAll();
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'fetch.all.feed',
                'target' => $this->feedService,
                'params' => ['where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name' => 'fetch.all.feed.post',
                'target' => $this->feedService,
                'params' => ['where' => null, 'prototype' => null, 'feeds' => $result],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchAllWhenEventStops()
    {
        $this->feedService->shouldReceive('fetchAll')
            ->never();
        $this->delegator->getEventManager()
            ->attach('fetch.all.feed', function (Event $event) {
                $event->stopPropagation(true);
            });

        $this->delegator->fetchAll();

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'fetch.all.feed',
                'target' => $this->feedService,
                'params' => ['where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchFeed()
    {
        $feed = new Feed(['feed_id' => 'es_friend_feed']);
        $this->feedService
            ->shouldReceive('fetchFeed')
            ->andReturn($feed);
        $this->delegator->fetchFeed('es_friend_feed');
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'fetch.feed',
                'target' => $this->feedService,
                'params' => ['feed_id' => 'es_friend_feed','where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name' => 'fetch.feed.post',
                'target' => $this->feedService,
                'params' => ['feed_id' => 'es_friend_feed', 'where' => null, 'prototype' => null, 'feed' => $feed],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchFeedWhenEventStops()
    {
        $this->feedService->shouldReceive('fetchFeed')
            ->never();
        $this->delegator->getEventManager()
            ->attach('fetch.feed', function (Event $event) {
                $event->stopPropagation(true);
            });

        $this->delegator->fetchFeed('es_friend_feed');

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'fetch.feed',
                'target' => $this->feedService,
                'params' => ['feed_id' => 'es_friend_feed','where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenFeedNotFound()
    {
        $exception = new NotFoundException('feed not found');
        $this->feedService
            ->shouldReceive('fetchFeed')
            ->andThrow($exception);

        $this->setExpectedException(NotFoundException::class);
        $this->delegator->fetchFeed('es_friend_feed');
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'fetch.feed',
                'target' => $this->feedService,
                'params' => ['feed_id' => 'es_friend_feed','where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name' => 'fetch.feed.error',
                'target' => $this->feedService,
                'params' => [
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
    public function testItShouldCallCreateFeed()
    {
        $feed = new Feed(['feed_id' => 'es_friend_feed']);
        $this->feedService
            ->shouldReceive('createFeed')
            ->with($feed)
            ->once();
        $this->delegator->createFeed($feed);
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'create.feed',
                'target' => $this->feedService,
                'params' => ['feed' => $feed],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name' => 'create.feed.post',
                'target' => $this->feedService,
                'params' => ['feed' => $feed],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallCreateFeedWhenEventStops()
    {
        $feed = new Feed(['feed_id' => 'es_friend_feed']);
        $this->feedService->shouldReceive('createFeed')
            ->never();
        $this->delegator->getEventManager()
            ->attach('create.feed', function (Event $event) {
                $event->stopPropagation(true);
            });

        $this->delegator->createFeed($feed);

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'create.feed',
                'target' => $this->feedService,
                'params' => ['feed' => $feed],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallUpdateFeed()
    {
        $feed = new Feed(['feed_id' => 'es_friend_feed']);
        $this->feedService
            ->shouldReceive('updateFeed')
            ->with($feed)
            ->once();
        $this->delegator->updateFeed($feed);
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'update.feed',
                'target' => $this->feedService,
                'params' => ['feed' => $feed],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name' => 'update.feed.post',
                'target' => $this->feedService,
                'params' => ['feed' => $feed],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallUpdateFeedWhenEventStops()
    {
        $feed = new Feed(['feed_id' => 'es_friend_feed']);
        $this->feedService->shouldReceive('updateFeed')
            ->never();
        $this->delegator->getEventManager()
            ->attach('update.feed', function (Event $event) {
                $event->stopPropagation(true);
            });

        $this->delegator->updateFeed($feed);

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'update.feed',
                'target' => $this->feedService,
                'params' => ['feed' => $feed],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallDeleteFeed()
    {
        $feed = new Feed(['feed_id' => 'es_friend_feed']);
        $this->feedService
            ->shouldReceive('deleteFeed')
            ->with($feed, true)
            ->once();
        $this->delegator->deleteFeed($feed);
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'delete.feed',
                'target' => $this->feedService,
                'params' => ['feed' => $feed, 'soft' => true],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name' => 'delete.feed.post',
                'target' => $this->feedService,
                'params' => ['feed' => $feed, 'soft' => true],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallDeleteFeedWhenEventStops()
    {
        $feed = new Feed(['feed_id' => 'es_friend_feed']);
        $this->feedService->shouldReceive('deleteFeed')
            ->never();
        $this->delegator->getEventManager()
            ->attach('delete.feed', function (Event $event) {
                $event->stopPropagation(true);
            });

        $this->delegator->deleteFeed($feed);

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name' => 'delete.feed',
                'target' => $this->feedService,
                'params' => ['feed' => $feed, 'soft' => true],
            ],
            $this->calledEvents[0]
        );
    }
}
