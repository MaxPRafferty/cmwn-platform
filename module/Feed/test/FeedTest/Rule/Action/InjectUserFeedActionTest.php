<?php

namespace FeedTest\Rule\Action;

use Feed\FeedInterface;
use Feed\Rule\Action\InjectUserFeedAction;
use Feed\Rule\Provider\FeedFromFeedableProvider;
use Feed\Service\FeedUserServiceInterface;
use Flip\Flip;
use Game\Game;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Rule\Exception\InvalidProviderType;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;
use User\Child;

/**
 * Unit tests for inject user feed action
 */
class InjectUserFeedActionTest extends TestCase
{
    /**
     * @var FeedUserServiceInterface | \Mockery|MockInterface
     */
    protected $feedUserService;

    /**
     * @before
     */
    public function setUpFeedUserService()
    {
        $this->feedUserService = \Mockery::mock(FeedUserServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldInjectFeedForUserIfUsersAreSet()
    {
        $flip = new Flip(['flip_id' => 'baz-bat']);
        $user = new Child(['user_id' => 'foobar12']);
        $provider = new FeedFromFeedableProvider($flip, $user);
        $feed = $provider->getValue();
        $this->assertInstanceOf(FeedInterface::class, $feed);
        $meta = $feed->getMeta();
        $this->assertArrayHasKey('users', $meta);
        $this->assertArrayHasKey('user_id', $meta['users']);
        $this->assertTrue(is_string($meta['users']['user_id']));

        $item = new BasicRuleItem(new BasicValueProvider('feed_provider', $feed));

        $this->feedUserService
            ->shouldReceive('attachFeedForUser')
            ->once();
        $action = new InjectUserFeedAction($this->feedUserService, 'feed_provider');
        $action($item);
    }

    /**
     * @test
     */
    public function testItShouldNotInjectUserFeedIfNoUsersAreSet()
    {
        $game = new Game(['game_id' => 'foo-bar']);
        $provider = new FeedFromFeedableProvider($game);
        $feed = $provider->getValue();
        $this->assertInstanceOf(FeedInterface::class, $feed);
        $meta = $feed->getMeta();
        $this->assertArrayNotHasKey('users', $meta);

        $item = new BasicRuleItem(new BasicValueProvider('feed_provider', $feed));

        $this->feedUserService
            ->shouldReceive('attachFeedForUser')
            ->never();
        $action = new InjectUserFeedAction($this->feedUserService, 'feed_provider');
        $action($item);
    }

    /**
     * @test
     */
    public function testItShouldThrowRuntimeExceptionIfFeedIsInvalid()
    {
        $item = new BasicRuleItem(new BasicValueProvider('feed_provider', null));

        $this->expectException(InvalidProviderType::class);
        $action = new InjectUserFeedAction($this->feedUserService, 'feed_provider');
        $action($item);
    }
}
