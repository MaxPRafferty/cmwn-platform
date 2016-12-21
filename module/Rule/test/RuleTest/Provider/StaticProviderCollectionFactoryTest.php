<?php

namespace RuleTest\Provider;

use Interop\Container\ContainerInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Exception\RuntimeException;
use Rule\Provider\BasicValueProvider;
use Rule\Provider\StaticProviderCollectionFactory;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Test StaticProviderCollectionFactoryTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StaticProviderCollectionFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBuildProviderCollectionWithSimpleArray()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')
            ->andReturn(false)
            ->byDefault();

        $items = [
            'active_user' => 'manchuck',
            'check_user'  => 'Adam',
        ];

        $collection = StaticProviderCollectionFactory::build($container, $items);

        $this->assertTrue(
            $collection->offsetExists('active_user'),
            'active_user was not built into in the provider collection from the static factory'
        );

        $this->assertEquals(
            'manchuck',
            $collection->offsetGet('active_user'),
            'active_user was not built into in the provider collection from the static factory'
        );

        $this->assertTrue(
            $collection->offsetExists('check_user'),
            'check_user was not built into in the provider collection from the static factory'
        );

        $this->assertEquals(
            'Adam',
            $collection->offsetGet('check_user'),
            'check_user was not built into in the provider collection from the static factory'
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildProviderCollectionWithProviderInItems()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')
            ->andReturn(false)
            ->byDefault();

        $items = [
            new TestProvider(),
        ];

        $collection = StaticProviderCollectionFactory::build($container, $items);

        $this->assertTrue(
            $collection->offsetExists('foo'),
            'foo was not built into in the provider collection from the static factory'
        );

        $this->assertEquals(
            'bar',
            $collection->offsetGet('foo'),
            'foo was not built into in the provider collection from the static factory'
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildProviderCollectionWithProviderInServices()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')
            ->with(TestProvider::class)
            ->andReturn(true)
            ->once();

        $container->shouldReceive('get')
            ->with(TestProvider::class)
            ->andReturn(new TestProvider());

        $items = [
            TestProvider::class,
        ];

        $collection = StaticProviderCollectionFactory::build($container, $items);

        $this->assertTrue(
            $collection->offsetExists('foo'),
            'foo was not built into in the provider collection from the static factory'
        );

        $this->assertEquals(
            'bar',
            $collection->offsetGet('foo'),
            'foo was not built into in the provider collection from the static factory'
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildProviderCollectionWithInvokableProvider()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')
            ->andThrow(ServiceNotFoundException::class)
            ->byDefault();

        $items = [
            TestProvider::class,
        ];

        $collection = StaticProviderCollectionFactory::build($container, $items);

        $this->assertTrue(
            $collection->offsetExists('foo'),
            'foo was not built into in the provider collection from the static factory'
        );

        $this->assertEquals(
            'bar',
            $collection->offsetGet('foo'),
            'foo was not built into in the provider collection from the static factory'
        );
    }

    /**
     * @test
     */
    public function testItShouldCreateCollectionWithRealUseCase()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')
            ->with('RuleTest\Provider\TestProvider')
            ->andReturn(false);

        $container->shouldReceive('has')
            ->with('manchuck')
            ->andReturn(false);

        $container->shouldReceive('has')
            ->with('Rule\Provider\BasicValueProvider')
            ->andReturn(false);

        $container->shouldReceive('has')
            ->with('Baz\\Bat\\Provider')
            ->andReturn(true);

        $container->shouldReceive('get')
            ->with('Baz\\Bat\\Provider')
            ->andReturn(new BasicValueProvider('baz', 'bat'));

        $items = [
            'Baz\\Bat\\Provider',
            TestProvider::class,
            new BasicValueProvider('fizz', 'buzz'),
            'active_user' => 'manchuck',
        ];

        $collection = StaticProviderCollectionFactory::build($container, $items);

        $this->assertEquals(
            'bar',
            $collection->offsetGet('foo'),
            'foo was not built into in the provider collection from the static factory'
        );

        $this->assertEquals(
            'bat',
            $collection->offsetGet('baz'),
            'bat was not built into in the provider collection from the static factory'
        );

        $this->assertEquals(
            'buzz',
            $collection->offsetGet('fizz'),
            'fizz was not built into in the provider collection from the static factory'
        );

        $this->assertEquals(
            'manchuck',
            $collection->offsetGet('active_user'),
            'active was not built into in the provider collection from the static factory'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenProvidedServiceIsNotAProvider()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')
            ->with('foo-bar')
            ->andReturn(true);

        $container->shouldReceive('get')
            ->with('foo-bar')
            ->andReturn(new \stdClass());

        $items = ['foo-bar'];

        $this->expectException(RuntimeException::class);
        StaticProviderCollectionFactory::build($container, $items);
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenInvokableClassIsNotAProvider()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')
            ->with(\stdClass::class)
            ->andReturn(false);

        $items = [
            \stdClass::class,
        ];

        $this->expectException(RuntimeException::class);
        StaticProviderCollectionFactory::build($container, $items);
    }
}
