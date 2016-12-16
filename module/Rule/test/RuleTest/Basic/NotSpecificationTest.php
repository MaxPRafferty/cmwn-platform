<?php

namespace RuleTest\Basic;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Basic\AlwaysSatisfiedRule;
use Rule\Basic\NeverSatisfiedRule;
use Rule\Basic\NotRule;
use Rule\Item\BasicRuleItem;

/**
 * Test NotSpecificationTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NotSpecificationTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenDifferentRuleIsNotHappy()
    {
        $notRule = new NotRule(new NeverSatisfiedRule());

        $this->assertTrue(
            $notRule->isSatisfiedBy(new BasicRuleItem()),
            'Not Rule Specification is not satisfied when other rule is not happy'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenDifferentRuleIsHappy()
    {
        $notRule = new NotRule(new AlwaysSatisfiedRule());

        $this->assertFalse(
            $notRule->isSatisfiedBy(new BasicRuleItem()),
            'Not Rule Specification is satisfied when other rule is happy'
        );
    }
}
