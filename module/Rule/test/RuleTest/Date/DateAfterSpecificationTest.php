<?php

namespace RuleTest\Date;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Date\DateAfterSpecification;
use Rule\RuleItemInterface;

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
        /** @var \Mockery\MockInterface|RuleItemInterface $item */
        $item      = \Mockery::mock(RuleItemInterface::class);
        $startDate = new \DateTime('-1 hour');
        $rule      = new DateAfterSpecification($startDate);

        $this->assertTrue(
            $rule->isSatisfiedBy($item),
            'Date After Rule was not satisfied when start date earlier than now'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenCurrentDateIsBeforeStartDate()
    {
        /** @var \Mockery\MockInterface|RuleItemInterface $item */
        $item      = \Mockery::mock(RuleItemInterface::class);
        $startDate = new \DateTime('+1 hour');
        $rule      = new DateAfterSpecification($startDate);

        $this->assertFalse(
            $rule->isSatisfiedBy($item),
            'Date After Rule was satisfied when start date later than now'
        );
    }
}
