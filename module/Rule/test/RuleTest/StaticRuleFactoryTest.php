<?php

namespace RuleTest;

use Interop\Container\ContainerInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Rule\Basic\AlwaysSatisfiedRule;
use Rule\Rule\Date\DateAfterRule;
use Rule\Exception\InvalidArgumentException;
use Rule\Rule\StaticRuleFactory;

/**
 * Test StaticRuleFactoryTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StaticRuleFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldCreateRuleFromClassWithNoOptions()
    {
        $rule = StaticRuleFactory::buildRuleFromClass(
            AlwaysSatisfiedRule::class
        );

        $this->assertInstanceOf(
            AlwaysSatisfiedRule::class,
            $rule,
            'Rule Factory did not build the rule from the class name'
        );
    }

    /**
     * @test
     */
    public function testItShouldCreateRuleFromClassWithOptions()
    {
        $rule = StaticRuleFactory::buildRuleFromClass(
            DateAfterRule::class,
            [new \DateTime]
        );

        $this->assertInstanceOf(
            DateAfterRule::class,
            $rule,
            'Rule Factory did not build the rule from the class name with options'
        );
    }

    /**
     * @test
     */
    public function testItShouldCreateRuleFromClassAndSerialize()
    {
        $rule = StaticRuleFactory::buildRuleFromClass(
            TestSerializedRule::class,
            'foo-bar'
        );

        $this->assertInstanceOf(
            TestSerializedRule::class,
            $rule,
            'Rule Factory did not build the rule with a string '
        );

        $this->assertEquals(
            'foo-bar',
            $rule->data,
            'Rule factory did not call unserialize on the rule'
        );
    }

    /**
     * @test
     */
    public function testItShouldCreateRuleFromContainerWithNoOptions()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container    = \Mockery::mock(ContainerInterface::class);
        $expectedRule = new TestSerializedRule();
        $container->shouldReceive('get')->with(TestSerializedRule::class)->andReturn($expectedRule);

        $actualRule = StaticRuleFactory::buildRuleFromContainer(
            $container,
            TestSerializedRule::class
        );

        $this->assertEquals(
            $expectedRule,
            $actualRule,
            'Rule Factory did not get the rule from the container'
        );
    }

    /**
     * @test
     */
    public function testItShouldCreateRuleFromContainerWithTextOptions()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container    = \Mockery::mock(ContainerInterface::class);
        $expectedRule = new TestSerializedRule();
        $container->shouldReceive('get')->with(TestSerializedRule::class)->andReturn($expectedRule);

        $actualRule = StaticRuleFactory::buildRuleFromContainer(
            $container,
            TestSerializedRule::class,
            "foo-bar"
        );

        $this->assertEquals(
            $expectedRule,
            $actualRule,
            'Rule Factory did not get the rule from the container'
        );

        $this->assertEquals(
            "foo-bar",
            $actualRule->data,
            'Rule Factory did not serialize the rule from the container'
        );
    }

    /**
     * @test
     */
    public function testItShouldCreateRuleFromContainerWithNonTextOptions()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container    = \Mockery::mock(ContainerInterface::class);
        $expectedRule = new TestSerializedRule();
        $container->shouldReceive('get')->with(TestSerializedRule::class)->andReturn($expectedRule);

        $actualRule = StaticRuleFactory::buildRuleFromContainer(
            $container,
            TestSerializedRule::class,
            ['foo' => 'bar']
        );

        $this->assertEquals(
            $expectedRule,
            $actualRule,
            'Rule Factory did not get the rule from the container'
        );

        $this->assertEquals(
            ['foo' => 'bar'],
            $actualRule->data,
            'Rule Factory did not serialize the rule from the container with an array'
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildFromContainerWithBuild()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container    = \Mockery::mock(ContainerInterface::class);
        $expectedRule = new TestSerializedRule();
        $container->shouldReceive('get')->with(TestSerializedRule::class)->andReturn($expectedRule);

        $actualRule = StaticRuleFactory::build(
            $container,
            TestSerializedRule::class,
            ['foo' => 'bar']
        );

        $this->assertEquals(
            $expectedRule,
            $actualRule,
            'Rule Factory did not get the rule from the container'
        );

        $this->assertEquals(
            ['foo' => 'bar'],
            $actualRule->data,
            'Rule Factory did not serialize the rule from the container with an array'
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildFromClassWithBuild()
    {
        $rule = StaticRuleFactory::build(
            DateAfterRule::class,
            [new \DateTime]
        );

        $this->assertInstanceOf(
            DateAfterRule::class,
            $rule,
            'Rule Factory did not build the rule from the class name with options'
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildFromClassWithBuildWhenContainerMissingService()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container          = \Mockery::mock(ContainerInterface::class);
        $expectedRule       = new TestSerializedRule();
        $expectedRule->data = ['foo' => 'bar'];
        $container->shouldReceive('get')->with(TestSerializedRule::class)->andThrow(new \Exception());

        $actualRule = StaticRuleFactory::build(
            $container,
            TestSerializedRule::class,
            ['foo' => 'bar']
        );

        $this->assertEquals(
            $expectedRule,
            $actualRule,
            'Rule Factory did not get the rule from the container'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenBuildRuleHasNoParameters()
    {
        $this->expectException(InvalidArgumentException::class);

        StaticRuleFactory::build();
    }
}
