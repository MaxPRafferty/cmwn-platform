<?php

namespace FeedTest\Rule\Provider;

use Feed\FeedInterface;
use Feed\Rule\Provider\FeedFromFeedableProvider;
use Flip\Flip;
use Game\Game;
use PHPUnit\Framework\TestCase;
use Rule\Exception\InvalidProviderType;
use User\Child;

/**
 * Unit Tests for feed from feedable provider
 */
class FeedFromFeedableProviderTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldCreateFeedFromFeedableWithUserNotSet()
    {
        $feedable = new Game(['game_id' => 'foo-bar']);
        $provider = new FeedFromFeedableProvider($feedable);
        $feed = $provider->getValue();
        $this->assertInstanceOf(FeedInterface::class, $feed);
        $this->assertEquals($feed->getType(), 'GAME');
        $this->assertEquals($feed->getTitle(), 'New Game is Added');
        $this->assertEquals($feed->getMessage(), 'New Game Added');
        $this->assertEquals($feed->getPriority(), '20');
        $this->assertEquals($feed->getMeta(), ['game_id' => 'foo-bar']);
    }

    /**
     * @test
     */
    public function testItShouldCreateFeedFromFeedableWithUserSet()
    {
        $feedable = new Flip(['flip_id' => 'foo-bar']);
        $provider = new FeedFromFeedableProvider($feedable, new Child(['user_id' => 'baz-bat']));
        $feed = $provider->getValue();
        $this->assertInstanceOf(FeedInterface::class, $feed);
        $this->assertEquals($feed->getPriority(), '5');
        $this->assertEquals($feed->getType(), 'FLIP');
        $this->assertEquals($feed->getTitle(), 'You have earned a new flip');
        $this->assertEquals($feed->getMessage(), 'Flip Earned');
        $this->assertEquals($feed->getMeta(), ['flip_id' => 'foo-bar', 'users' => ['user_id' => 'baz-bat']]);
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionIfFeedableNotSetCorrectly()
    {
        $provider = new FeedFromFeedableProvider();
        $this->expectException(InvalidProviderType::class);
        $provider->getValue();
    }
}
