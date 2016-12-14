<?php

namespace RuleTest\Basic;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Basic\AndSpecification;
use Rule\RuleItemInterface;
use Rule\RuleInterface;

/**
 * Test AndSpecificationTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AndSpecificationTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldSatisfyTwoRules()
    {
        /** @var \Mockery\MockInterface|RuleItemInterface $item */
        /** @var \Mockery\MockInterface|RuleInterface $ruleOne */
        /** @var \Mockery\MockInterface|RuleInterface $ruleTwo */
        $item    = \Mockery::mock(RuleItemInterface::class);
        $ruleOne = \Mockery::mock(RuleInterface::class);
        $ruleTwo = \Mockery::mock(RuleInterface::class);

        $ruleOne->shouldReceive('isSatisfiedBy')->with($item)->andReturn(true)->once();
        $ruleTwo->shouldReceive('isSatisfiedBy')->with($item)->andReturn(true)->once();

        $andRule = new AndSpecification(
            $ruleOne,
            $ruleTwo
        );

        $this->assertTrue(
            $andRule->isSatisfiedBy($item),
            'And Rule Specification did not satisfy 2 rules that are satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotSatisfyRulesWhenOneIsNotHappy()
    {
        /** @var \Mockery\MockInterface|RuleItemInterface $item */
        /** @var \Mockery\MockInterface|RuleInterface $ruleOne */
        /** @var \Mockery\MockInterface|RuleInterface $ruleTwo */
        $item    = \Mockery::mock(RuleItemInterface::class);
        $ruleOne = \Mockery::mock(RuleInterface::class);
        $ruleTwo = \Mockery::mock(RuleInterface::class);

        $ruleOne->shouldReceive('isSatisfiedBy')->with($item)->andReturn(true)->once();
        $ruleTwo->shouldReceive('isSatisfiedBy')->with($item)->andReturn(false)->once();

        $andRule = new AndSpecification(
            $ruleOne,
            $ruleTwo
        );

        $this->assertFalse(
            $andRule->isSatisfiedBy($item),
            'And Rule Specification satisfied 2 rules where one is not satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldSatisfyThreeRules()
    {
        /** @var \Mockery\MockInterface|RuleItemInterface $item */
        /** @var \Mockery\MockInterface|RuleInterface $ruleOne */
        /** @var \Mockery\MockInterface|RuleInterface $ruleTwo */
        /** @var \Mockery\MockInterface|RuleInterface $ruleThree */
        $item      = \Mockery::mock(RuleItemInterface::class);
        $ruleOne   = \Mockery::mock(RuleInterface::class);
        $ruleTwo   = \Mockery::mock(RuleInterface::class);
        $ruleThree = \Mockery::mock(RuleInterface::class);

        $ruleOne->shouldReceive('isSatisfiedBy')->with($item)->andReturn(true)->once();
        $ruleTwo->shouldReceive('isSatisfiedBy')->with($item)->andReturn(true)->once();
        $ruleThree->shouldReceive('isSatisfiedBy')->with($item)->andReturn(true)->once();

        $andRule = new AndSpecification(
            $ruleOne,
            $ruleTwo,
            $ruleThree
        );

        $this->assertTrue(
            $andRule->isSatisfiedBy($item),
            'And Rule Specification did not satisfy 3 rules that are satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotSatisfyThreeRulesWhenTwoAreNotHappy()
    {
        /** @var \Mockery\MockInterface|RuleItemInterface $item */
        /** @var \Mockery\MockInterface|RuleInterface $ruleOne */
        /** @var \Mockery\MockInterface|RuleInterface $ruleTwo */
        /** @var \Mockery\MockInterface|RuleInterface $ruleThree */
        $item      = \Mockery::mock(RuleItemInterface::class);
        $ruleOne   = \Mockery::mock(RuleInterface::class);
        $ruleTwo   = \Mockery::mock(RuleInterface::class);
        $ruleThree = \Mockery::mock(RuleInterface::class);

        $ruleOne->shouldReceive('isSatisfiedBy')->with($item)->andReturn(true)->once();
        $ruleTwo->shouldReceive('isSatisfiedBy')->with($item)->andReturn(false)->once();
        $ruleThree->shouldReceive('isSatisfiedBy')->with($item)->andReturn(false)->once();

        $andRule = new AndSpecification(
            $ruleOne,
            $ruleTwo,
            $ruleThree
        );

        $this->assertFalse(
            $andRule->isSatisfiedBy($item),
            'And Rule Specification satisfied 2 rules where 2 are not satisfied'
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

        $andRule = new AndSpecification(
            $ruleOne,
            $ruleTwo,
            $ruleThree
        );

        $this->assertFalse(
            $andRule->isSatisfiedBy($item),
            'And Rule Specification satisfied when all rules are not happy'
        );
    }
}
