<?php

namespace RuleTest\Rule\Collection;

use PHPUnit\Framework\TestCase as TestCase;
use Rule\Rule\Basic\AlwaysSatisfiedRule;
use Rule\Rule\Basic\NeverSatisfiedRule;
use Rule\Item\BasicRuleItem;
use Rule\Rule\Collection\RuleCollection;

/**
 * Test RuleCollectionTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RuleCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldAddAndRulesByDefault()
    {
        $collection = new RuleCollection();
        $collection->append(new AlwaysSatisfiedRule())
            ->append(new AlwaysSatisfiedRule());

        $this->assertTrue(
            $collection->isSatisfiedBy(new BasicRuleItem()),
            'Rule Collection should add all rules as the AND operator'
        );

        $collection->append(new NeverSatisfiedRule());
        $this->assertFalse(
            $collection->isSatisfiedBy(new BasicRuleItem()),
            'Appended rule to collection with no operator was not added with an AND'
        );
    }

    /**
     * @test
     */
    public function testItShouldAddNotRulesByDefault()
    {
        $collection = new RuleCollection();
        $collection->append(new NeverSatisfiedRule(), RuleCollection::OPERATOR_NOT)
            ->append(new NeverSatisfiedRule(), RuleCollection::OPERATOR_NOT);

        $this->assertTrue(
            $collection->isSatisfiedBy(new BasicRuleItem()),
            'Rule collection did not respect the not operator'
        );

        $collection->append(new NeverSatisfiedRule());
        $this->assertFalse(
            $collection->isSatisfiedBy(new BasicRuleItem()),
            'Appended "NOT" rule was satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldAddEitherRulesWithOneOrGroup()
    {
        $collection = new RuleCollection();
        $collection->append(
            new AlwaysSatisfiedRule(),
            RuleCollection::OPERATOR_OR,
            'group_1'
        );

        $collection->append(
            new NeverSatisfiedRule(),
            RuleCollection::OPERATOR_OR,
            'group_1'
        );

        $this->assertTrue(
            $collection->isSatisfiedBy(new BasicRuleItem()),
            'Rule Collection should add all rules as the AND operator'
        );
    }

    /**
     * @test
     */
    public function testItShouldAddEitherRulesWithMultipleOrGroups()
    {
        $collection = new RuleCollection();
        $collection->append(
            new AlwaysSatisfiedRule(),
            RuleCollection::OPERATOR_OR,
            'group_1'
        );

        $collection->append(
            new NeverSatisfiedRule(),
            RuleCollection::OPERATOR_OR,
            'group_1'
        );

        // -- AND --

        $collection->append(
            new AlwaysSatisfiedRule(),
            RuleCollection::OPERATOR_OR,
            'group_2'
        );

        $collection->append(
            new NeverSatisfiedRule(),
            RuleCollection::OPERATOR_OR,
            'group_2'
        );

        $this->assertTrue(
            $collection->isSatisfiedBy(new BasicRuleItem()),
            'Rule Collection should add all rules as the AND operator'
        );
    }
    /**
     * @test
     */
    public function testItShouldAddEitherRulesWithMultipleOrGroupsAndOneFails()
    {
        $collection = new RuleCollection();
        $collection->append(
            new AlwaysSatisfiedRule(),
            RuleCollection::OPERATOR_OR,
            'group_1'
        );

        $collection->append(
            new NeverSatisfiedRule(),
            RuleCollection::OPERATOR_OR,
            'group_1'
        );

        // -- AND --

        $collection->append(
            new NeverSatisfiedRule(),
            RuleCollection::OPERATOR_OR,
            'group_2'
        );

        $collection->append(
            new NeverSatisfiedRule(),
            RuleCollection::OPERATOR_OR,
            'group_2'
        );

        $this->assertFalse(
            $collection->isSatisfiedBy(new BasicRuleItem()),
            'Rule Collection should add all rules as the AND operator'
        );
    }

    /**
     * @test
     */
    public function testItShouldPassWhenSameCollectionIsPassedMultipleTimes()
    {
        $collection = new RuleCollection();
        $collection->append(new AlwaysSatisfiedRule())
            ->append(new NeverSatisfiedRule());

        $this->assertFalse(
            $collection->isSatisfiedBy(new BasicRuleItem()),
            'Rule Collection should fail on the first call'
        );

        $this->assertFalse(
            $collection->isSatisfiedBy(new BasicRuleItem()),
            'Rule Collection should fail on the second call'
        );
    }
}
