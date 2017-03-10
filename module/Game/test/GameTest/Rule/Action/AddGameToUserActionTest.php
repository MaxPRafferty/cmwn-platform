<?php

namespace GameTest\Rule\Action;

use Game\Game;
use Game\Rule\Action\AddGameToUserAction;
use Game\Service\UserGameServiceInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Exception\InvalidProviderType;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;
use User\UserInterface;

/**
 * Test AddGameToUserActionTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AddGameToUserActionTest extends TestCase
{
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
    public function testItShouldAddGameToUserWithDefaultProvider()
    {
        $action = new AddGameToUserAction(
            $this->userGameService,
            'foo-bar'
        );

        /** @var \Mockery\MockInterface|UserInterface $user */
        $user = \Mockery::mock(UserInterface::class);
        $item = new BasicRuleItem(new BasicValueProvider('active_user', $user));

        $this->userGameService->shouldReceive('attachGameToUser')
            ->once()
            ->andReturnUsing(function ($actualUser, $actualGame) use (&$user) {
                $expectedGame = new Game();
                $expectedGame->setGameId('foo-bar');
                $this->assertEquals(
                    $expectedGame,
                    $actualGame,
                    AddGameToUserAction::class . ' did not add the expected game'
                );

                $this->assertEquals(
                    $user,
                    $actualUser,
                    AddGameToUserAction::class . ' did not add the correct user'
                );

                return true;
            });

        $action($item);
    }

    /**
     * @test
     */
    public function testItShouldAddGameToUserWithCustomProvider()
    {
        $action = new AddGameToUserAction(
            $this->userGameService,
            'foo-bar',
            'fizz-buzz'
        );

        /** @var \Mockery\MockInterface|UserInterface $user */
        $user = \Mockery::mock(UserInterface::class);
        $item = new BasicRuleItem(new BasicValueProvider('fizz-buzz', $user));

        $this->userGameService->shouldReceive('attachGameToUser')
            ->once()
            ->andReturnUsing(function ($actualUser, $actualGame) use (&$user) {
                $expectedGame = new Game();
                $expectedGame->setGameId('foo-bar');
                $this->assertEquals(
                    $expectedGame,
                    $actualGame,
                    AddGameToUserAction::class . ' did not add the expected game'
                );

                $this->assertEquals(
                    $user,
                    $actualUser,
                    AddGameToUserAction::class . ' did not add the correct user'
                );

                return true;
            });


        $action($item);
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenUserInvalid()
    {
        $action = new AddGameToUserAction(
            $this->userGameService,
            'foo-bar'
        );

        $user = new \stdClass();
        $item = new BasicRuleItem(new BasicValueProvider('active_user', $user));

        $this->userGameService->shouldReceive('attachGameToUser')
            ->never();

        $this->expectException(InvalidProviderType::class);
        $action($item);
    }
}
