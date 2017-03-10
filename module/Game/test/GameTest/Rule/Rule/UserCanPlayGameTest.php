<?php

namespace GameTest\Rule\Rule;

use Application\Exception\NotFoundException;
use Game\Game;
use Game\Rule\Rule\UserCanPlayGame;
use Game\Service\UserGameServiceInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Exception\InvalidProviderType;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;
use User\UserInterface;

/**
 * Test UserCanPlayGameTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserCanPlayGameTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|UserGameServiceInterface
     */
    protected $userGameService;

    /**
     * @before
     */
    public function setUpUserGameServiceInterface()
    {
        $this->userGameService = \Mockery::mock(UserGameServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenUserCanPlayGameWithDefaultProvider()
    {
        /** @var \Mockery\MockInterface|UserInterface $user */
        $user = \Mockery::mock(UserInterface::class);

        $this->userGameService->shouldReceive('fetchGameForUser')
            ->once()
            ->andReturnUsing(function ($actualUser, $actualGame) use (&$user) {
                $expectedGame = new Game();
                $expectedGame->setGameId('foo-bar');
                $this->assertEquals(
                    $expectedGame,
                    $actualGame,
                    AddGameToUserAction::class . ' called fetchGameForUser with wrong game'
                );

                $this->assertEquals(
                    $user,
                    $actualUser,
                    AddGameToUserAction::class . ' called fetchGameForUser with the wrong user'
                );

                return $expectedGame;
            });

        $rule = new UserCanPlayGame(
            $this->userGameService,
            'foo-bar'
        );

        $this->assertEquals(
            0,
            $rule->timesSatisfied(),
            UserCanPlayGame::class . ' starts off satisfied'
        );

        $this->assertTrue(
            $rule->isSatisfiedBy(new BasicRuleItem(new BasicValueProvider('active_user', $user))),
            UserCanPlayGame::class . ' ain\'t got no satisfaction'
        );

        $this->assertEquals(
            1,
            $rule->timesSatisfied(),
            UserCanPlayGame::class . ' is reporting incorrect satisfaction count'
        );
    }

    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenUserCanPlayGameWithCustomProvider()
    {
        /** @var \Mockery\MockInterface|UserInterface $user */
        $user = \Mockery::mock(UserInterface::class);

        $this->userGameService->shouldReceive('fetchGameForUser')
            ->once()
            ->andReturnUsing(function ($actualUser, $actualGame) use (&$user) {
                $expectedGame = new Game();
                $expectedGame->setGameId('foo-bar');
                $this->assertEquals(
                    $expectedGame,
                    $actualGame,
                    AddGameToUserAction::class . ' called fetchGameForUser with wrong game'
                );

                $this->assertEquals(
                    $user,
                    $actualUser,
                    AddGameToUserAction::class . ' called fetchGameForUser with the wrong user'
                );

                return $expectedGame;
            });

        $rule = new UserCanPlayGame(
            $this->userGameService,
            'foo-bar',
            'fizz-buzz'
        );

        $this->assertEquals(
            0,
            $rule->timesSatisfied(),
            UserCanPlayGame::class . ' starts off satisfied'
        );

        $this->assertTrue(
            $rule->isSatisfiedBy(new BasicRuleItem(new BasicValueProvider('fizz-buzz', $user))),
            UserCanPlayGame::class . ' ain\'t got no satisfaction'
        );

        $this->assertEquals(
            1,
            $rule->timesSatisfied(),
            UserCanPlayGame::class . ' is reporting incorrect satisfaction count'
        );
    }

    /**
     * @test
     */
    public function testItShouldBeNotBeSatisfiedWhenUserCannotPlayGame()
    {
        /** @var \Mockery\MockInterface|UserInterface $user */
        $user = \Mockery::mock(UserInterface::class);

        $this->userGameService->shouldReceive('fetchGameForUser')
            ->once()
            ->andThrow(new NotFoundException());

        $rule = new UserCanPlayGame(
            $this->userGameService,
            'foo-bar'
        );

        $this->assertEquals(
            0,
            $rule->timesSatisfied(),
            UserCanPlayGame::class . ' starts off satisfied'
        );

        $this->assertFalse(
            $rule->isSatisfiedBy(new BasicRuleItem(new BasicValueProvider('active_user', $user))),
            UserCanPlayGame::class . ' is satisfied even though user cannot play game'
        );

        $this->assertEquals(
            0,
            $rule->timesSatisfied(),
            UserCanPlayGame::class . ' is reporting incorrect satisfaction count'
        );
    }
    /**
     * @test
     */
    public function testItShouldThrowExceptionWheUserProvidedIsIncorrectType()
    {
        $user = new \stdClass();
        $this->userGameService->shouldReceive('fetchGameForUser')
            ->never();

        $rule = new UserCanPlayGame(
            $this->userGameService,
            'foo-bar'
        );

        $this->expectException(InvalidProviderType::class);
        $rule->isSatisfiedBy(new BasicRuleItem(new BasicValueProvider('active_user', $user)));
    }

}
