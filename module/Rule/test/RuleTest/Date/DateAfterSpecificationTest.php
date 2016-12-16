<?php

namespace RuleTest\Date;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Date\DateAfterRule;
use Rule\Item\BasicRuleItem;

/**
 * Test DateAfterSpecificationTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DateAfterSpecificationTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenCurrentDateIsAfterStartDate()
    {
        $startDate = new \DateTime('-1 hour');
        $rule      = new DateAfterRule($startDate);

        $this->assertTrue(
            $rule->isSatisfiedBy(new BasicRuleItem()),
            'Date After Rule was not satisfied when start date earlier than now'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenCurrentDateIsBeforeStartDate()
    {
        $startDate = new \DateTime('+1 hour');
        $rule      = new DateAfterRule($startDate);

        $this->assertFalse(
            $rule->isSatisfiedBy(new BasicRuleItem()),
            'Date After Rule was satisfied when start date later than now'
        );
    }
}
