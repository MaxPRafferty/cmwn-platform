<?php

namespace IntegrationTest\Service;

use Application\Exception\NotFoundException;
use Feed\Service\FeedService;
use Feed\Service\FeedUserService;
use Feed\Service\FeedUserServiceInterface;
use Feed\UserFeed;
use IntegrationTest\AbstractDbTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;

/**
 * Class FeedUserServiceTest
 * @package IntegrationTest\Service
 */
class FeedUserServiceTest extends TestCase
{
    /**
     * @var FeedUserServiceInterface $feedUserService
     */
    protected $feedUserService;

    /**
     * @var FeedService $feedService
     */
    protected $feedService;

    /**
     * return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../DataSets/feed.dataset.php');
    }

    /**
     * @before
     */
    public function setUpFeedUserService()
    {
        $this->feedUserService = TestHelper::getServiceManager()->get(FeedUserService::class);
        $this->feedService = TestHelper::getServiceManager()->get(FeedService::class);
    }

    /**
     * @test
     */
    public function testItShouldFetchAllFeedForUser()
    {
        $feeds = $this->feedUserService->fetchAllFeedForUser('english_student');
        $feeds = $feeds->getItems(0, $feeds->count());
        $expected = ['game_feed', 'es_friend_feed', 'es_flip_feed'];
        $actual = [];
        foreach ($feeds as $userFeed) {
            $actual [] = $userFeed->getFeedId();
        }
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testItShouldFetchFeedForUser()
    {
        $feed = $this->feedUserService->fetchFeedForUser('english_student', 'es_friend_feed');
        $this->assertEquals('math_student', $feed->getSender());
        $this->assertEquals([], $feed->getMeta());
        $this->assertEquals('Friendship Made', $feed->getTitle());
        $this->assertEquals('became friends with', $feed->getMessage());
        $this->assertEquals(5, $feed->getPriority());
        $this->assertEquals(2, $feed->getVisibility());
        $this->assertEquals(1, $feed->getTypeVersion());
        $this->assertEquals('2016-04-15 11:49:08', $feed->getPosted());
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenFeedNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->assertInstanceOf(
            NotFoundException::class,
            $this->feedUserService->fetchFeedForUser('english_student', 'foobar'),
            'exception not thrown'
        );
    }

    /**
     * @test
     */
    public function testItShouldAttachFeedToUser()
    {
        $this->feedUserService->attachFeedForUser('other_student', new UserFeed(['feed_id' => 'es_friend_feed']));
        $feeds = $this->feedUserService->fetchAllFeedForUser('other_student');
        $actual = [];
        $feeds = $feeds->getItems(0, $feeds->count());
        foreach ($feeds as $userFeed) {
            $actual [] = $userFeed->getFeedId();
        }
        $this->assertContains('es_friend_feed', $actual, 'feed not attached correctly');
    }

    /**
     * @test
     */
    public function testItShouldUpdateUserFeed()
    {
        $userFeed = $this->feedUserService->fetchFeedForUser('english_student', 'es_friend_feed');
        $this->assertEquals(0, $userFeed->getReadFlag());

        $userFeed->setReadFlag(1);
        $this->feedUserService->updateFeedForUser('english_student', $userFeed);

        $userFeedAterUpdate = $this->feedUserService->fetchFeedForUser('english_student', 'es_friend_feed');
        $this->assertEquals(1, $userFeedAterUpdate->getReadFlag());
    }

    /**
     * @test
     */
    public function testItShouldDeleteUserFeed()
    {
        $userFeed = $this->feedUserService->fetchFeedForUser('english_student', 'es_friend_feed');

        $this->feedUserService->deleteFeedForUser('english_student', $userFeed);

        $this->expectException(NotFoundException::class);

        $this->assertInstanceOf(
            NotFoundException::class,
            $this->feedUserService->fetchFeedForUser('english_student', 'es_friend_feed'),
            'user feed not deleted'
        );
    }
}
