<?php

namespace RuleTest\Rule\Basic;

use PHPUnit\Framework\TestCase as TestCase;
use Rule\Rule\Basic\AlwaysSatisfiedRule;
use Rule\Rule\Basic\EitherRule;
use Rule\Rule\Basic\NeverSatisfiedRule;
use Rule\Item\BasicRuleItem;

/**
 * Test EitherSpecificationTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EitherRuleTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldSatisfyWithTwoRules()
    {
        $ruleOne = new AlwaysSatisfiedRule();
        $ruleTwo = new AlwaysSatisfiedRule();

        $eitherRule = new EitherRule(
            $ruleOne,
            $ruleTwo
        );

        $this->assertTrue(
            $eitherRule->isSatisfiedBy(new BasicRuleItem()),
            'Either Rule Specification did not satisfy 2 rules that are satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldSatisfyRulesWhenNoneAreHappy()
    {
        $ruleOne = new NeverSatisfiedRule();
        $ruleTwo = new NeverSatisfiedRule();

        $eitherRule = new EitherRule(
            $ruleOne,
            $ruleTwo
        );

        $this->assertFalse(
            $eitherRule->isSatisfiedBy(new BasicRuleItem()),
            'Either Rule Specification satisfied 2 rules where one is not satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldSatisfyThreeRulesWhenOneIsHappy()
    {
        $ruleOne   = new NeverSatisfiedRule();
        $ruleTwo   = new AlwaysSatisfiedRule();
        $ruleThree = new NeverSatisfiedRule();

        $eitherRule = new EitherRule(
            $ruleOne,
            $ruleTwo,
            $ruleThree
        );

        $this->assertTrue(
            $eitherRule->isSatisfiedBy(new BasicRuleItem()),
            'Either Rule Specification did not satisfy 3 rules that are satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotSatisfyThreeRulesWhenNoneAreHappy()
    {
        $ruleOne   = new NeverSatisfiedRule();
        $ruleTwo   = new NeverSatisfiedRule();
        $ruleThree = new NeverSatisfiedRule();

        $eitherRule = new EitherRule(
            $ruleOne,
            $ruleTwo,
            $ruleThree
        );

        $this->assertFalse(
            $eitherRule->isSatisfiedBy(new BasicRuleItem()),
            'Either Rule Specification satisfied when all rules are not happy'
        );
    }

    /**
     * @test
     */
    public function testItShouldSatisfyALargeNumberOfRulesWhenAllAreHappy()
    {
        $rules      = array_fill(0, 1000, new AlwaysSatisfiedRule());
        $eitherRule = new EitherRule(...$rules);

        $this->assertTrue(
            $eitherRule->isSatisfiedBy(new BasicRuleItem()),
            'And Rule Specification did not satisfy 1000 rules'
        );

        $this->assertEquals(
            1000,
            $eitherRule->timesSatisfied(),
            '100% of These rules should be satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldSatisfyALargeNumberOfRulesWithTwentyPercentAreHappy()
    {
        $rules = array_merge(
            array_fill(0, 200, new AlwaysSatisfiedRule()),
            array_fill(0, 800, new NeverSatisfiedRule())
        );

        shuffle($rules);
        $eitherRule = new EitherRule(...$rules);

        $this->assertTrue(
            $eitherRule->isSatisfiedBy(new BasicRuleItem()),
            'Either Rule Specification was not satisfied all rules when 80% are not happy'
        );

        $this->assertEquals(
            200,
            $eitherRule->timesSatisfied(),
            '20% of these rules should have passed'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotSatisfyALargeNumberOfRulesWhenNoneAreHappy()
    {
        $rules      = array_fill(0, 1000, new NeverSatisfiedRule());
        $eitherRule = new EitherRule(...$rules);

        $this->assertFalse(
            $eitherRule->isSatisfiedBy(new BasicRuleItem()),
            'Either Rule Specification not satisfied all rules when all rules are not happy'
        );

        $this->assertEquals(
            0,
            $eitherRule->timesSatisfied(),
            'None of these rules should have have passed'
        );
    }
}
