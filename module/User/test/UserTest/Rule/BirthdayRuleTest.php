<?php

namespace UserTest\Rule;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;
use User\Child;
use User\Rule\BirthdayRule;

/**
 * Test BirthdayRuleTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BirthdayRuleTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenItIsTheUsersBirthday()
    {
        $user = new Child();
        $user->setBirthdate(new \DateTime('now'));
        $ruleItem = new BasicRuleItem(
            new BasicValueProvider('check_user', $user)
        );
        $rule     = new BirthdayRule();
        $this->assertTrue(
            $rule->isSatisfiedBy($ruleItem),
            'Birthday Rule was not satisfied on the users birthday.  How Rude!'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenUsersBirthdayIsTomorrow()
    {
        $user = new Child();
        $user->setBirthdate(new \DateTime('tomorrow'));
        $ruleItem = new BasicRuleItem(
            new BasicValueProvider('check_user', $user)
        );
        $rule     = new BirthdayRule();
        $this->assertFalse(
            $rule->isSatisfiedBy($ruleItem),
            'Birthday Rule was satisfied the day before a users birthday'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenUsersBirthdayWasYesterday()
    {
        $user = new Child();
        $user->setBirthdate(new \DateTime('yesterday'));
        $ruleItem = new BasicRuleItem(
            new BasicValueProvider('check_user', $user)
        );
        $rule     = new BirthdayRule();
        $this->assertFalse(
            $rule->isSatisfiedBy($ruleItem),
            'Birthday Rule was satisfied the day after a users birthday'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenCheckUserIsMissing()
    {
        $ruleItem = new BasicRuleItem();
        $rule     = new BirthdayRule();
        $this->assertFalse(
            $rule->isSatisfiedBy($ruleItem),
            'Birthday Rule was satisfied when the check user is missing'
        );
    }
}
