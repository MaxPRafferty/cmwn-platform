<?php

namespace RuleTest\Date;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Date\DateBetweenSpecification;
use Rule\RuleItemInterface;

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
        /** @var \Mockery\MockInterface|RuleItemInterface $item */
        $item      = \Mockery::mock(RuleItemInterface::class);
        $startDate = new \DateTime('-1 hour');
        $endDate   = new \DateTime('+1 hour');
        $rule      = new DateBetweenSpecification($startDate, $endDate);

        $this->assertTrue(
            $rule->isSatisfiedBy($item),
            'Date Between Rule was not satisfied when current date is between start and end date'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenCurrentDateBeforeStartDate()
    {
        /** @var \Mockery\MockInterface|RuleItemInterface $item */
        $item      = \Mockery::mock(RuleItemInterface::class);
        $startDate = new \DateTime('+1 hour');
        $endDate   = new \DateTime('+2 hours');
        $rule      = new DateBetweenSpecification($startDate, $endDate);

        $this->assertFalse(
            $rule->isSatisfiedBy($item),
            'Date Between Rule was satisfied when current date is before start date'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenCurrentDateAfterEndDate()
    {
        /** @var \Mockery\MockInterface|RuleItemInterface $item */
        $item      = \Mockery::mock(RuleItemInterface::class);
        $startDate = new \DateTime('-2 hours');
        $endDate   = new \DateTime('-1 hour');
        $rule      = new DateBetweenSpecification($startDate, $endDate);

        $this->assertFalse(
            $rule->isSatisfiedBy($item),
            'Date Between Rule was satisfied when current date is after end date'
        );
    }
}
