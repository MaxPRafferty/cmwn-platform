<?php

namespace RuleTest\Rule\Collection;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Item\BasicRuleItem;
use Rule\Rule\Basic\AlwaysSatisfiedRule;
use Rule\Rule\Basic\NeverSatisfiedRule;
use Rule\Rule\Basic\NotRule;
use Rule\Rule\Collection\RuleCollectionInterface;
use Rule\Rule\Collection\StaticRuleCollectionFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * Test StaticRuleCollectionFactoryTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StaticRuleCollectionFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBuildACollectionWithJustArraySpecification()
    {
        $collection = StaticRuleCollectionFactory::build(
            new ServiceManager(),
            [
                [
                    'rule'     => ['name' => NeverSatisfiedRule::class],
                    'operator' => RuleCollectionInterface::OPERATOR_NOT,
                ],
                [
                    'rule' => ['name' => AlwaysSatisfiedRule::class],
                ],
            ]
        );

        $this->assertTrue(
            $collection->isSatisfiedBy(new BasicRuleItem()),
            'Collection factory did not create the correct collection'
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildACollecitonWithRules()
    {
        $collection = StaticRuleCollectionFactory::build(
            new ServiceManager(),
            [new AlwaysSatisfiedRule(), new AlwaysSatisfiedRule()]
        );

        $this->assertTrue(
            $collection->isSatisfiedBy(new BasicRuleItem()),
            'Collection factory did not create the correct collection with rules'
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildACollectionWithRulesAndSpec()
    {
        $collection = StaticRuleCollectionFactory::build(
            new ServiceManager(),
            [
                [
                    'rule'     => ['name' => NeverSatisfiedRule::class],
                    'operator' => RuleCollectionInterface::OPERATOR_NOT,
                ],
                new AlwaysSatisfiedRule()
            ]
        );

        $this->assertTrue(
            $collection->isSatisfiedBy(new BasicRuleItem()),
            'Collection factory did not create the correct collection with mixed array and rules'
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildACollectionWithRulesSpecAndServiceManager()
    {
        $service = new ServiceManager();
        $service->setService(AlwaysSatisfiedRule::class, new AlwaysSatisfiedRule());

        $collection = StaticRuleCollectionFactory::build(
            new ServiceManager(),
            [
                [
                    'rule'     => ['name' => NeverSatisfiedRule::class],
                    'operator' => RuleCollectionInterface::OPERATOR_NOT,
                ],
                new NotRule(new NeverSatisfiedRule()),
                [
                    'rule'     => ['name' => AlwaysSatisfiedRule::class],
                ],
            ]
        );

        $this->assertTrue(
            $collection->isSatisfiedBy(new BasicRuleItem()),
            'Collection factory did not create the correct collection with mixed specification'
        );
    }
}
