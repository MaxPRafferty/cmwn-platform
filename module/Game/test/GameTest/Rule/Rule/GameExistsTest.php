<?php

namespace GameTest\Rule\Rule;

use Application\Exception\NotFoundException;
use Game\Game;
use Game\Rule\Rule\GameExists;
use Game\Service\GameServiceInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Item\BasicRuleItem;

/**
 * Test GameExistsTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GameExistsTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|GameServiceInterface
     */
    protected $gameService;

    /**
     * @before
     */
    public function setUpGameServiceInterface()
    {
        $this->gameService = \Mockery::mock(GameServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenGameExists()
    {
        $rule = new GameExists(
            $this->gameService,
            'foo-bar'
        );

        $this->gameService->shouldReceive('fetchGame')
            ->with('foo-bar')
            ->andReturn(new Game())
            ->once();

        $this->assertEquals(
            0,
            $rule->timesSatisfied(),
            GameExists::class . ' starts off satisfied'
        );

        $this->assertTrue(
            $rule->isSatisfiedBy(new BasicRuleItem()),
            GameExists::class . ' ain\'t go no satisfaction'
        );

        $this->assertEquals(
            1,
            $rule->timesSatisfied(),
            GameExists::class . ' is not reporting correct number of times satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenGameExists()
    {
        $rule = new GameExists(
            $this->gameService,
            'foo-bar'
        );

        $this->gameService->shouldReceive('fetchGame')
            ->with('foo-bar')
            ->andThrow(new NotFoundException())
            ->once();

        $this->assertEquals(
            0,
            $rule->timesSatisfied(),
            GameExists::class . ' starts off satisfied'
        );

        $this->assertFalse(
            $rule->isSatisfiedBy(new BasicRuleItem()),
            GameExists::class . ' is satisfied when the game is not found'
        );

        $this->assertEquals(
            0,
            $rule->timesSatisfied(),
            GameExists::class . ' is not reporting correct number of times satisfied'
        );
    }

}
