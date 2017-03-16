<?php

namespace IntegrationTest\Service;

use Application\Exception\NotFoundException;
use Feed\Feed;
use Feed\FeedInterface;
use Feed\Service\FeedService;
use IntegrationTest\AbstractDbTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;

/**
 * Class FeedServiceTest
 * @package IntegrationTest\Service
 */
class FeedServiceTest extends TestCase
{
    /**
     * return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../DataSets/feed.dataset.php');
    }

    /**
     * @var FeedService
     */
    protected $feedService;

    /**
     * @before
     */
    public function setUpFeedService()
    {
        $this->feedService = TestHelper::getServiceManager()->get(FeedService::class);
    }

    /**
     * @test
     */
    public function testItShouldFetchFeedById()
    {
        try {
            $feed = $this->feedService->fetchFeed('es_friend_feed');
            $this->assertEquals('math_student', $feed->getSender());
            $this->assertEquals([], $feed->getMeta());
            $this->assertEquals('Friendship Made', $feed->getTitle());
            $this->assertEquals('became friends with', $feed->getMessage());
            $this->assertEquals(5, $feed->getPriority());
            $this->assertEquals(2, $feed->getVisibility());
            $this->assertEquals(1, $feed->getTypeVersion());
            $this->assertEquals('2016-04-15 11:49:08', $feed->getPosted());
        } catch (NotFoundException $nf) {
            $this->fail('it did not fetch feed with valid feed_id');
        }
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenFeedNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->assertInstanceOf(
            NotFoundException::class,
            $this->feedService->fetchFeed('foobar'),
            'it did not throw exception'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllFeedInTheOrderOfPriority()
    {
        $feeds = $this->feedService->fetchAll();
        $feeds = $feeds->getItems(0, $feeds->count());

        $actual = [];
        $expected = [
            'es_friend_feed',
            'ms_friend_feed',
            'es_game_feed',
            'ms_game_feed',
            'os_game_feed',
            'es_flip_feed',
            'ms_flip_feed',
            'os_flip_feed'
        ];
        foreach ($feeds as $feed) {
            $this->assertInstanceOf(FeedInterface::class, $feed);
            $actual[] = $feed->getFeedId();
        }

        $this->assertEquals($expected, $actual, 'it did not fetch all feeds correctly');
    }

    /**
     * @test
     */
    public function testItShouldCreateFeed()
    {
        $feed = new Feed([
            'sender'       => 'other_student',
            'title'        => 'Friendship Made',
            'message'      => 'became friends with',
            'priority'     => 3,
            'posted'       => '2016-04-15 11:49:08',
            'visibility'   => 2,
            'type'         => 'FLIP',
            'type_version' => 1,
        ]);

        $this->feedService->createFeed($feed);

        $this->assertNotNull($feed->getFeedId(), 'feed not created correctly');

        $feed = $this->feedService->fetchFeed($feed->getFeedId());

        $this->assertEquals('other_student', $feed->getSender());
        $this->assertEquals([], $feed->getMeta());
        $this->assertEquals('Friendship Made', $feed->getTitle());
        $this->assertEquals('became friends with', $feed->getMessage());
        $this->assertEquals(3, $feed->getPriority());
        $this->assertEquals(2, $feed->getVisibility());
        $this->assertEquals(1, $feed->getTypeVersion());
        $this->assertEquals('2016-04-15 11:49:08', $feed->getPosted());
    }

    /**
     * @test
     */
    public function testItShouldUpdateFeed()
    {
        $feed = $feed = new Feed([
            'feed_id'      => 'os_flip_feed',
            'sender'       => 'other_student',
            'title'        => 'Flip Earned',
            'message'      => 'you earned a new flip',
            'priority'     => 5,
            'posted'       => '2016-04-15 11:49:08',
            'visibility'   => 4,
            'type'         => 'FLIP',
            'type_version' => 2,
        ]);

        $this->feedService->updateFeed($feed);

        $feedAfter = $this->feedService->fetchFeed('os_flip_feed');
        $this->assertEquals($feedAfter->getVisibility(), 4);
        $this->assertEquals($feedAfter->getPriority(), 5);
        $this->assertEquals($feedAfter->getTypeVersion(), 2);
    }

    /**
     * @test
     */
    public function testItShouldHardDeleteFeed()
    {
        $this->feedService->deleteFeed(new Feed(['feed_id' => 'es_friend_feed']), false);
        $this->expectException(NotFoundException::class);
        $this->assertInstanceOf(
            NotFoundException::class,
            $this->feedService->fetchFeed('es_friend_feed'),
            'feed not deleted'
        );
    }

    /**
     * @test
     */
    public function testItShouldSoftDeleteFeed()
    {
        $this->feedService->deleteFeed(new Feed(['feed_id' => 'es_friend_feed']));
        $feed = $this->feedService->fetchFeed('es_friend_feed');
        $this->assertNotNull($feed->getDeleted());
    }
}
