<?php

namespace SecurityTest\Rule\Rule;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Exception\InvalidProviderType;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;
use Security\GuestUser;
use Security\Rule\Rule\HasRole;
use User\Adult;

/**
 * Test HasRoleTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class HasRoleTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBeSatisifiedWhenRoleMatches()
    {
        $user = new GuestUser();
        $item = new BasicRuleItem(new BasicValueProvider('active_user', $user));
        $rule = new HasRole('guest', 'active_user');

        $this->assertTrue(
            $rule->isSatisfiedBy($item),
            HasRole::class . ' is not happy when the role matches the expected'
        );

        $this->assertEquals(
            1,
            $rule->timesSatisfied(),
            HasRole::class . ' should be satisfied 1 time with this item'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisifedWhenRoleDoesNotMatch()
    {
        $user = new GuestUser();
        $item = new BasicRuleItem(new BasicValueProvider('check_user', $user));
        $rule = new HasRole('super', 'check_user');

        $this->assertFalse(
            $rule->isSatisfiedBy($item),
            HasRole::class . ' is happy when the role does not matche the expected'
        );

        $this->assertEquals(
            0,
            $rule->timesSatisfied(),
            HasRole::class . ' should be satisfied 0 times with this item'
        );
    }

    /**
     * @test
     */
    public function testItShouldFailWhenProviderIsNotASecurityUser()
    {
        $user = new Adult();
        $item = new BasicRuleItem(new BasicValueProvider('check_user', $user));
        $rule = new HasRole('super', 'check_user');

        $this->expectException(InvalidProviderType::class);
        $rule->isSatisfiedBy($item);
    }
}
