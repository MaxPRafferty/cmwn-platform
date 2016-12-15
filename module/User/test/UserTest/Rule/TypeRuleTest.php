<?php

namespace UserTest\Rule;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\RuleItem;
use User\Child;
use User\Rule\TypeRule;
use User\UserInterface;

/**
 * Test TypeRuleTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TypeRuleTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenUserMatchesType()
    {
        $user     = new Child();
        $ruleItem = new RuleItem(['check_user' => $user]);

        $rule = new TypeRule(UserInterface::TYPE_CHILD);
        $this->assertTrue(
            $rule->isSatisfiedBy($ruleItem),
            'Type Rule for a child was not satisfied by a child'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenUserDoesNotMatchType()
    {
        $user     = new Child();
        $ruleItem = new RuleItem(['check_user' => $user]);

        $rule = new TypeRule(UserInterface::TYPE_ADULT);
        $this->assertFalse(
            $rule->isSatisfiedBy($ruleItem),
            'Type Rule for an adult was satisfied by a child'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenCheckUserIsNotInRuleItem()
    {
        $ruleItem  = new RuleItem();
        $childRule = new TypeRule(UserInterface::TYPE_CHILD);
        $this->assertFalse(
            $childRule->isSatisfiedBy($ruleItem),
            'Type Rule for a child was satisfied when missing check user'
        );

        $adultRule = new TypeRule(UserInterface::TYPE_ADULT);
        $this->assertFalse(
            $adultRule->isSatisfiedBy($ruleItem),
            'Type Rule for an adult was satisfied when missing check user'
        );
    }
}
