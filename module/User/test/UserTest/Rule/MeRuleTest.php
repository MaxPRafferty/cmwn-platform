<?php

namespace UserTest\Rule;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\RuleItem;
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
        $item       = new RuleItem(['active_user' => $activeUser, 'check_user' => $checkUser]);
        $rule       = new MeRule();

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
        $item       = new RuleItem(['active_user' => $activeUser, 'check_user' => $checkUser]);
        $rule       = new MeRule();

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
        $checkUser  = new Adult(['user_id' => 'baz-bat']);
        $item       = new RuleItem(['check_user' => $checkUser]);
        $rule       = new MeRule();

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
        $item       = new RuleItem(['active_user' => $activeUser]);
        $rule       = new MeRule();

        $this->assertFalse(
            $rule->isSatisfiedBy($item),
            'Me Rule was satisfied missing the active_user'
        );
    }
}
