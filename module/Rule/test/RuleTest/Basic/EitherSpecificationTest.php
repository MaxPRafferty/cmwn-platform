<?php

namespace RuleTest\Basic;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Basic\EitherSpecification;
use Rule\RuleItemInterface;
use Rule\RuleInterface;

/**
 * Test EitherSpecificationTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EitherSpecificationTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldSatisfyWithTwoRules()
    {
        /** @var \Mockery\MockInterface|RuleItemInterface $item */
        /** @var \Mockery\MockInterface|RuleInterface $ruleOne */
        /** @var \Mockery\MockInterface|RuleInterface $ruleTwo */
        $item    = \Mockery::mock(RuleItemInterface::class);
        $ruleOne = \Mockery::mock(RuleInterface::class);
        $ruleTwo = \Mockery::mock(RuleInterface::class);

        $ruleOne->shouldReceive('isSatisfiedBy')->with($item)->andReturn(true)->once();
        $ruleTwo->shouldReceive('isSatisfiedBy')->with($item)->andReturn(true)->once();

        $eitherRule = new EitherSpecification(
            $ruleOne,
            $ruleTwo
        );

        $this->assertTrue(
            $eitherRule->isSatisfiedBy($item),
            'Either Rule Specification did not satisfy 2 rules that are satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldSatisfyRulesWhenNoneAreHappy()
    {
        /** @var \Mockery\MockInterface|RuleItemInterface $item */
        /** @var \Mockery\MockInterface|RuleInterface $ruleOne */
        /** @var \Mockery\MockInterface|RuleInterface $ruleTwo */
        $item    = \Mockery::mock(RuleItemInterface::class);
        $ruleOne = \Mockery::mock(RuleInterface::class);
        $ruleTwo = \Mockery::mock(RuleInterface::class);

        $ruleOne->shouldReceive('isSatisfiedBy')->with($item)->andReturn(false)->once();
        $ruleTwo->shouldReceive('isSatisfiedBy')->with($item)->andReturn(false)->once();

        $eitherRule = new EitherSpecification(
            $ruleOne,
            $ruleTwo
        );

        $this->assertFalse(
            $eitherRule->isSatisfiedBy($item),
            'Either Rule Specification satisfied 2 rules where one is not satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldSatisfyThreeRulesWhenOneIsHappy()
    {
        /** @var \Mockery\MockInterface|RuleItemInterface $item */
        /** @var \Mockery\MockInterface|RuleInterface $ruleOne */
        /** @var \Mockery\MockInterface|RuleInterface $ruleTwo */
        /** @var \Mockery\MockInterface|RuleInterface $ruleThree */
        $item      = \Mockery::mock(RuleItemInterface::class);
        $ruleOne   = \Mockery::mock(RuleInterface::class);
        $ruleTwo   = \Mockery::mock(RuleInterface::class);
        $ruleThree = \Mockery::mock(RuleInterface::class);

        $ruleOne->shouldReceive('isSatisfiedBy')->with($item)->andReturn(false)->once();
        $ruleTwo->shouldReceive('isSatisfiedBy')->with($item)->andReturn(true)->once();
        $ruleThree->shouldReceive('isSatisfiedBy')->with($item)->andReturn(false)->once();

        $eitherRule = new EitherSpecification(
            $ruleOne,
            $ruleTwo,
            $ruleThree
        );

        $this->assertTrue(
            $eitherRule->isSatisfiedBy($item),
            'Either Rule Specification did not satisfy 3 rules that are satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotSatisfyThreeRulesWhenNoneAreHappy()
    {
        /** @var \Mockery\MockInterface|RuleItemInterface $item */
        /** @var \Mockery\MockInterface|RuleInterface $ruleOne */
        /** @var \Mockery\MockInterface|RuleInterface $ruleTwo */
        /** @var \Mockery\MockInterface|RuleInterface $ruleThree */
        $item      = \Mockery::mock(RuleItemInterface::class);
        $ruleOne   = \Mockery::mock(RuleInterface::class);
        $ruleTwo   = \Mockery::mock(RuleInterface::class);
        $ruleThree = \Mockery::mock(RuleInterface::class);

        $ruleOne->shouldReceive('isSatisfiedBy')->with($item)->andReturn(false)->once();
        $ruleTwo->shouldReceive('isSatisfiedBy')->with($item)->andReturn(false)->once();
        $ruleThree->shouldReceive('isSatisfiedBy')->with($item)->andReturn(false)->once();

        $eitherRule = new EitherSpecification(
            $ruleOne,
            $ruleTwo,
            $ruleThree
        );

        $this->assertFalse(
            $eitherRule->isSatisfiedBy($item),
            'Either Rule Specification satisfied when all rules are not happy'
        );
    }
}
