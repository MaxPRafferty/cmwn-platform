<?php

namespace RuleTest\Action;

use Interop\Container\ContainerInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Action\NoopAction;
use Rule\Action\StaticActionFactory;
use Rule\Exception\InvalidArgumentException;
use Rule\Exception\RuntimeException;

/**
 * Test StaticActionFactoryTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StaticActionFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBuildFromContainer()
    {
        $action = new NoopAction();

        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')
            ->once()
            ->with(NoopAction::class)
            ->andReturn(true);

        $container->shouldReceive('get')
            ->once()
            ->with(NoopAction::class)
            ->andReturn($action);

        $this->assertSame(
            $action,
            StaticActionFactory::build($container, NoopAction::class),
            'Static Action Factory did not return action from Service Container'
        );
    }

    /**
     * @test
     */
    public function testItShouldCreateInvokableActionWithNoOptions()
    {
        $this->assertInstanceOf(
            NoopAction::class,
            StaticActionFactory::build(NoopAction::class),
            'Static Action Factory did not build action from class'
        );
    }

    /**
     * @test
     */
    public function testItShouldFallBackToInvokableWhenActionNotInContainer()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')
            ->once()
            ->with(NoopAction::class)
            ->andReturn(false);

        $container->shouldReceive('get')
            ->never()
            ->with(NoopAction::class);

        $this->assertInstanceOf(
            NoopAction::class,
            StaticActionFactory::build($container, NoopAction::class),
            'Static Action Factory did not build action from class'
        );
    }

    /**
     * @test
     */
    public function testItShouldSerializeDataFromContainer()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $expected  = new TestAction();
        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')
            ->once()
            ->with(TestAction::class)
            ->andReturn(true);

        $container->shouldReceive('get')
            ->once()
            ->with(TestAction::class)
            ->andReturn($expected);

        $actual = StaticActionFactory::build($container, TestAction::class, ['foo' => 'bar']);
        $this->assertEquals(
            $expected,
            $actual,
            'Static Action Factory did not return action from Service Container'
        );

        $this->assertEquals(
            ['foo' => 'bar'],
            $actual->serializeData,
            'Static action factory did not serialize data'
        );
    }

    /**
     * @test
     */
    public function testItShouldSerializeDataFromInvokable()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $actual   = StaticActionFactory::build(TestAction::class, 'foo-bar');
        $this->assertInstanceOf(
            TestAction::class,
            $actual,
            'Static Action Factory did not return action from Service Container'
        );

        $this->assertEquals(
            'foo-bar',
            $actual->serializeData,
            'Static action factory did not serialize data'
        );
    }

    /**
     * @test
     */
    public function testItShouldPassThroughDataIntoConstorFromInvokable()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $actual   = StaticActionFactory::build(TestAction::class, ['foo', 'bar']);
        $this->assertInstanceOf(
            TestAction::class,
            $actual,
            'Static Action Factory did not return action from Service Container'
        );

        $this->assertEquals(
            ['foo', 'bar'],
            $actual->constructData,
            'Static action factory did not serialize data'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenNameFromServiceIsNotARule()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')
            ->once()
            ->with(\stdClass::class)
            ->andReturn(true);

        $container->shouldReceive('get')
            ->once()
            ->with(\stdClass::class)
            ->andReturn(new \stdClass());

        $this->setExpectedException(
            RuntimeException::class,
            'The service stdClass is not a valid action'
        );

        StaticActionFactory::build($container, \stdClass::class);
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenInvokableIsNotARule()
    {
        $this->setExpectedException(
            RuntimeException::class,
            'The class stdClass is not an action'
        );

        StaticActionFactory::build(\stdClass::class);
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenInvokableClassDoesNotExist()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'Class foobar is not found'
        );

        StaticActionFactory::build('foobar');
    }
}
