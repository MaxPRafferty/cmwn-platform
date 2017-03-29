<?php

namespace RuleTest\Rule\Date;

use Application\Utils\Date\DateTimeFactory;
use PHPUnit\Framework\TestCase;
use Rule\Rule\Date\DateBetweenRule;
use Rule\Item\BasicRuleItem;

/**
 * Test DateBetweenSpecificationTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DateBetweenSpecificationTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenCurrentDateInRange()
    {
        $startDate = new \DateTime('-1 hour');
        $endDate   = new \DateTime('+1 hour');
        $rule      = new DateBetweenRule($startDate, $endDate);

        $this->assertTrue(
            $rule->isSatisfiedBy(new BasicRuleItem()),
            'Date Between Rule was not satisfied when current date is between start and end date'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenCurrentDateBeforeStartDate()
    {
        $startDate = new \DateTime('+1 hour');
        $endDate   = new \DateTime('+2 hours');
        $rule      = new DateBetweenRule($startDate, $endDate);

        $this->assertFalse(
            $rule->isSatisfiedBy(new BasicRuleItem()),
            'Date Between Rule was satisfied when current date is before start date'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenCurrentDateAfterEndDate()
    {
        $startDate = new \DateTime('-2 hours');
        $endDate   = new \DateTime('-1 hour');
        $rule      = new DateBetweenRule($startDate, $endDate);

        $this->assertFalse(
            $rule->isSatisfiedBy(new BasicRuleItem()),
            'Date Between Rule was satisfied when current date is after end date'
        );
    }

    /**
     * @test
     */
    public function testItShouldForABirthday()
    {
        $birthday  = DateTimeFactory::factory('now');
        $startDate = clone $birthday;
        $endDate   = clone $birthday;

        $startDate->setTime(0, 0, 0);
        $endDate->setTime(23, 59, 59);
        $rule = new DateBetweenRule($startDate, $endDate);

        $this->assertTrue(
            $rule->isSatisfiedBy(new BasicRuleItem()),
            'Date Between Rule was not satisfied for a birthday'
        );
    }
}
