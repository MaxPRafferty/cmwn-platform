<?php

namespace FeedTest\Rule\Action;

use Feed\Rule\Action\InjectFeedAction;
use Feed\Service\FeedServiceInterface;
use Flip\Flip;
use Game\Game;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Rule\Exception\InvalidProviderType;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;
use User\Child;

/**
 * Unit tests for InjectFeedAction
 */
class InjectFeedActionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface | FeedServiceInterface
     */
    protected $feedService;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->feedService = \Mockery::mock(FeedServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldInjectFeedWithUser()
    {
        $flip = new Flip(['flip_id' => 'foo-bar']);
        $user = new Child(['user_id' => 'baz-bat']);
        $item = new BasicRuleItem(new BasicValueProvider('feedable', $flip), new BasicValueProvider('user', $user));
        $this->feedService->shouldReceive('createFeed')
            ->once();
        $action = new InjectFeedAction($this->feedService);

        $action($item);
    }

    /**
     * @test
     */
    public function testItShouldInjectFeedWithoutUser()
    {
        $game = new Game(['game_id' => 'foo-bar']);
        $item = new BasicRuleItem(new BasicValueProvider('feedable', $game));
        $this->feedService->shouldReceive('createFeed')
            ->once();
        $action = new InjectFeedAction($this->feedService);

        $action($item);
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenFeedableIsInvalid()
    {
        $item = new BasicRuleItem();
        $this->feedService->shouldReceive('createFeed')
            ->never();

        $this->expectException(InvalidProviderType::class);
        $action = new InjectFeedAction($this->feedService);

        $action($item);
    }
}
