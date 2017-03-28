<?php

namespace GameTest\Rule\Rule;

use Game\Game;
use Game\Rule\Rule\GameComingSoonRule;
use PHPUnit\Framework\TestCase;
use Rule\Exception\InvalidProviderType;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;

/**
 * Unit tests for game coming soon rule
 */
class GameComingSoonRuleTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBeSatisfiedIfGameIsNotComingSoon()
    {
        $item = new BasicRuleItem(new BasicValueProvider('game', new Game(
            [
                'game_id' => 'foo-bar',
                'coming_soon' => false
            ]
        )));

        $rule = new GameComingSoonRule();

        $this->assertTrue($rule->isSatisfiedBy($item));
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedIfGameIsComingSoon()
    {
        $item = new BasicRuleItem(new BasicValueProvider('game', new Game(
            [
                'game_id' => 'foo-bar',
                'coming_soon' => true
            ]
        )));

        $rule = new GameComingSoonRule();

        $this->assertFalse($rule->isSatisfiedBy($item));
    }

    /**
     * @test
     */
    public function testItShouldThrowRuntimeExceptionIfGameIsInvalid()
    {
        $item = new BasicRuleItem();

        $rule = new GameComingSoonRule();

        $this->expectException(InvalidProviderType::class);

        $rule->isSatisfiedBy($item);
    }
}
