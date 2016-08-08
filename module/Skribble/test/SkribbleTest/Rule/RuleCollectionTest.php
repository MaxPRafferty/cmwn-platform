<?php

namespace SkribbleTest\Rule;

use \PHPUnit_Framework_TestCase as TestCase;
use Skribble\Rule\Item;
use Skribble\Rule\RuleCollection;

/**
 * Test RuleCollectionTest
 *
 * @group Skribble
 * @group SkribbleRule
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RuleCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldOnlyAllowRulesToBeAdded()
    {
        $collection = new RuleCollection('items');

        $rule = new Item();

        $collection->offsetSet(null, $rule);
        $this->assertSame(
            $rule,
            $collection->offsetGet(0),
            'Collection did not take in rule for offsetGet'
        );

        $collection->append($rule);
        $this->assertSame(
            $rule,
            $collection->offsetGet(1),
            'Collection did not take in rule on append'
        );

        $this->assertTrue($collection->isValid(), 'Collection should be valid when all rules are valid');
        $this->assertEquals('items', $collection->getRuleType());
    }

    /**
     * @test
     */
    public function testItShouldExtractArrayCorrectly()
    {
        $collection = new RuleCollection('items');

        $rule = new Item();
        $collection->append($rule);

        $expected = [$rule->getArrayCopy()];

        $this->assertEquals($expected, $collection->getArrayCopy(), 'Rules cannot be extracted correctly');
    }

    /**
     * @test
     */
    public function testItShouldBeAbleToHydrateItSelf()
    {
        $this->markTestSkipped('bcmath not installed');
        $expected = new RuleCollection('items');

        $rule = new Item();
        $expected->append($rule);

        $actual = new RuleCollection('items', $expected->getArrayCopy());
        $this->assertEquals(
            $rule->getArrayCopy(),
            $actual->offsetGet(0)->getArrayCopy(),
            'Collection cannot be hydrated from itself'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenNonRulePassedIn()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $collection = new RuleCollection('item');
        $collection->append(new \stdClass());
    }
}
