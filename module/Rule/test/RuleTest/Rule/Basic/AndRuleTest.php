<?php

namespace RuleTest\Rule\Basic;

use PHPUnit\Framework\TestCase;
use Rule\Rule\Basic\AlwaysSatisfiedRule;
use Rule\Rule\Basic\AndRule;
use Rule\Rule\Basic\NeverSatisfiedRule;
use Rule\Item\BasicRuleItem;

/**
 * Test AndSpecificationTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AndRuleTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldSatisfyTwoRules()
    {
        $ruleOne = new AlwaysSatisfiedRule();
        $ruleTwo = new AlwaysSatisfiedRule();

        $andRule = new AndRule(
            $ruleOne,
            $ruleTwo
        );

        $this->assertTrue(
            $andRule->isSatisfiedBy(new BasicRuleItem()),
            'And Rule Specification did not satisfy 2 rules that are satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotSatisfyRulesWhenOneIsNotHappy()
    {
        $ruleOne = new AlwaysSatisfiedRule();
        $ruleTwo = new NeverSatisfiedRule();

        $andRule = new AndRule(
            $ruleOne,
            $ruleTwo
        );

        $this->assertFalse(
            $andRule->isSatisfiedBy(new BasicRuleItem()),
            'And Rule Specification satisfied 2 rules where one is not satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldSatisfyThreeRules()
    {
        $ruleOne   = new AlwaysSatisfiedRule();
        $ruleTwo   = new AlwaysSatisfiedRule();
        $ruleThree = new AlwaysSatisfiedRule();

        $andRule = new AndRule(
            $ruleOne,
            $ruleTwo,
            $ruleThree
        );

        $this->assertTrue(
            $andRule->isSatisfiedBy(new BasicRuleItem()),
            'And Rule Specification did not satisfy 3 rules that are satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotSatisfyThreeRulesWhenTwoAreNotHappy()
    {
        $ruleOne   = new AlwaysSatisfiedRule();
        $ruleTwo   = new NeverSatisfiedRule();
        $ruleThree = new NeverSatisfiedRule();

        $andRule = new AndRule(
            $ruleOne,
            $ruleTwo,
            $ruleThree
        );

        $this->assertFalse(
            $andRule->isSatisfiedBy(new BasicRuleItem()),
            'And Rule Specification satisfied 2 rules where 2 are not satisfied'
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

        $andRule = new AndRule(
            $ruleOne,
            $ruleTwo,
            $ruleThree
        );

        $this->assertFalse(
            $andRule->isSatisfiedBy(new BasicRuleItem()),
            'And Rule Specification satisfied when all rules are not happy'
        );
    }

    /**
     * @test
     */
    public function testItShouldSatisfyArrayOfRulesWhenAllAreHappy()
    {
        $rules   = array_fill(0, 1000, new AlwaysSatisfiedRule());
        $andRule = new AndRule(...$rules);

        $this->assertTrue(
            $andRule->isSatisfiedBy(new BasicRuleItem()),
            'And Rule Specification did not satisfy 1000 rules'
        );

        $this->assertEquals(
            1000,
            $andRule->timesSatisfied(),
            '100% of These rules should be satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotSatisfyArrayOfRulesWhenSomeAreNotHappy()
    {
        $rules = array_merge(
            array_fill(0, 200, new AlwaysSatisfiedRule()),
            array_fill(0, 800, new NeverSatisfiedRule())
        );

        shuffle($rules);
        $andRule = new AndRule(...$rules);

        $this->assertFalse(
            $andRule->isSatisfiedBy(new BasicRuleItem()),
            'And Rule Specification satisfied all rules when 80% are not happy'
        );

        $this->assertEquals(
            200,
            $andRule->timesSatisfied(),
            '20% of these rules should have passed'
        );
    }
}
