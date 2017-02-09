<?php

namespace UserTest\Rule;

use PHPUnit\Framework\TestCase as TestCase;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;
use User\Adult;
use User\Rule\MeRule;

/**
 * Test MeRuleTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MeRuleTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenTheActiveUserMatchesTheCheckUser()
    {
        $activeUser = new Adult(['user_id' => 'foo-bar']);
        $checkUser  = new Adult(['user_id' => 'foo-bar']);
        $rule       = new MeRule();
        $item       = new BasicRuleItem(
            new BasicValueProvider('check_user', $checkUser),
            new BasicValueProvider('active_user', $activeUser)
        );

        $this->assertTrue(
            $rule->isSatisfiedBy($item),
            'Me Rule was not satisfied when active_user matches the check_user'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenTheActiveUserDoesNotMatchTheCheckUser()
    {
        $activeUser = new Adult(['user_id' => 'foo-bar']);
        $checkUser  = new Adult(['user_id' => 'baz-bat']);
        $rule       = new MeRule();
        $item       = new BasicRuleItem(
            new BasicValueProvider('check_user', $checkUser),
            new BasicValueProvider('active_user', $activeUser)
        );

        $this->assertFalse(
            $rule->isSatisfiedBy($item),
            'Me Rule was satisfied when active_user has a different id than the check_user'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenMissingTheActiveUser()
    {
        $checkUser = new Adult(['user_id' => 'baz-bat']);
        $rule      = new MeRule();
        $item      = new BasicRuleItem(
            new BasicValueProvider('check_user', $checkUser)
        );

        $this->assertFalse(
            $rule->isSatisfiedBy($item),
            'Me Rule was satisfied missing the active_user'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenMissingTheCheckUser()
    {
        $activeUser = new Adult(['user_id' => 'foo-bar']);
        $rule       = new MeRule();
        $item       = new BasicRuleItem(
            new BasicValueProvider('active_user', $activeUser)
        );

        $this->assertFalse(
            $rule->isSatisfiedBy($item),
            'Me Rule was satisfied missing the active_user'
        );
    }
}
