<?php

namespace RuleTest\Basic;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Basic\NotSpecification;
use Rule\RuleItemInterface;
use Rule\RuleInterface;

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
        /** @var \Mockery\MockInterface|RuleItemInterface $item */
        /** @var \Mockery\MockInterface|RuleInterface $rule */
        $item = \Mockery::mock(RuleItemInterface::class);
        $rule = \Mockery::mock(RuleInterface::class);

        $rule->shouldReceive('isSatisfiedBy')->with($item)->andReturn(false)->once();

        $notRule = new NotSpecification($rule);

        $this->assertTrue(
            $notRule->isSatisfiedBy($item),
            'Not Rule Specification is not satisfied when other rule is not happy'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenDifferentRuleIsHappy()
    {
        /** @var \Mockery\MockInterface|RuleItemInterface $item */
        /** @var \Mockery\MockInterface|RuleInterface $rule */
        $item = \Mockery::mock(RuleItemInterface::class);
        $rule = \Mockery::mock(RuleInterface::class);

        $rule->shouldReceive('isSatisfiedBy')->with($item)->andReturn(true)->once();

        $notRule = new NotSpecification($rule);

        $this->assertFalse(
            $notRule->isSatisfiedBy($item),
            'Not Rule Specification is satisfied when other rule is happy'
        );
    }
}
