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


    /**
     * @test
     */
    public function testItShouldSatisfyALargeNumberOfRulesWhenAllAreHappy()
    {
        /** @var \Mockery\MockInterface|RuleItemInterface $event */
        $event = \Mockery::mock(RuleItemInterface::class);
        $rules = [];
        foreach (range(0, 999) as $ruleCount) {
            $rule = \Mockery::mock(RuleInterface::class);
            $rule->shouldReceive('isSatisfiedBy')
                ->with($event)
                ->andReturn(true)
                ->once();
            array_push($rules, $rule);
        }

        $eitherRule = new EitherSpecification(...$rules);

        $this->assertTrue(
            $eitherRule->isSatisfiedBy($event),
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
        /** @var \Mockery\MockInterface|RuleItemInterface $event */
        $event = \Mockery::mock(RuleItemInterface::class);
        $rules = [];
        foreach (range(0, 999) as $ruleCount) {
            $rule = \Mockery::mock(RuleInterface::class);
            $rule->shouldReceive('isSatisfiedBy')
                ->with($event)
                ->andReturn($ruleCount % 5 == 0)
                ->once();
            array_push($rules, $rule);
        }

        $eitherRule = new EitherSpecification(...$rules);

        $this->assertTrue(
            $eitherRule->isSatisfiedBy($event),
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
        /** @var \Mockery\MockInterface|RuleItemInterface $event */
        $event = \Mockery::mock(RuleItemInterface::class);
        $rules = [];
        foreach (range(0, 999) as $ruleCount) {
            $rule = \Mockery::mock(RuleInterface::class);
            $rule->shouldReceive('isSatisfiedBy')
                ->with($event)
                ->andReturn(false)
                ->once();
            array_push($rules, $rule);
        }

        $eitherRule = new EitherSpecification(...$rules);

        $this->assertFalse(
            $eitherRule->isSatisfiedBy($event),
            'Either Rule Specification not satisfied all rules when all rules are not happy'
        );

        $this->assertEquals(
            0,
            $eitherRule->timesSatisfied(),
            'None of these rules should have have passed'
        );
    }
}
