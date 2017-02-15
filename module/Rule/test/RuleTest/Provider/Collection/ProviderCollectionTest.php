<?php

namespace RuleTest\Provider\Collection;

use PHPUnit\Framework\TestCase;
use Rule\Provider\BasicValueProvider;
use Rule\Provider\Collection\ProviderCollection;

/**
 * Test ProviderCollectionTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProviderCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldProvideAllValuesFromConstruct()
    {
        $providerOne   = new BasicValueProvider('foo', 'bar');
        $providerTwo   = new BasicValueProvider('baz', 'bat');
        $providerThree = new BasicValueProvider('fizz', 'buzz');
        $collection    = new ProviderCollection(
            $providerOne,
            $providerTwo,
            $providerThree
        );

        $this->assertTrue(
            $collection->offsetExists('foo'),
            'Foo was not provided in the provider collection'
        );

        $this->assertEquals(
            'bar',
            $collection->offsetGet('foo'),
            'Foo has bad value in the provider collection'
        );

        $this->assertTrue(
            $collection->offsetExists('baz'),
            'baz was not provided in the provider collection'
        );

        $this->assertEquals(
            'bat',
            $collection->offsetGet('baz'),
            'baz has bad value in the provider collection'
        );

        $this->assertTrue(
            $collection->offsetExists('fizz'),
            'fizz was not provided in the provider collection'
        );

        $this->assertEquals(
            'buzz',
            $collection->offsetGet('fizz'),
            'fizz has bad value in the provider collection'
        );
    }

    /**
     * @test
     */
    public function testItShouldProvideAllValuesThatAreAppended()
    {
        $providerOne   = new BasicValueProvider('foo', 'bar');
        $providerTwo   = new BasicValueProvider('baz', 'bat');
        $providerThree = new BasicValueProvider('fizz', 'buzz');
        $collection    = new ProviderCollection();
        $collection->append($providerOne);
        $collection->append($providerTwo);
        $collection->append($providerThree);

        $this->assertTrue(
            $collection->offsetExists('foo'),
            'Foo was not provided in the provider collection'
        );

        $this->assertEquals(
            'bar',
            $collection->offsetGet('foo'),
            'Foo has bad value in the provider collection'
        );

        $this->assertTrue(
            $collection->offsetExists('baz'),
            'baz was not provided in the provider collection'
        );

        $this->assertEquals(
            'bat',
            $collection->offsetGet('baz'),
            'baz has bad value in the provider collection'
        );

        $this->assertTrue(
            $collection->offsetExists('fizz'),
            'fizz was not provided in the provider collection'
        );

        $this->assertEquals(
            'buzz',
            $collection->offsetGet('fizz'),
            'fizz has bad value in the provider collection'
        );
    }

    /**
     * @test
     */
    public function testItShouldProvideAllValuesThatAreAppendedAndConstructed()
    {
        $providerOne   = new BasicValueProvider('foo', 'bar');
        $providerTwo   = new BasicValueProvider('baz', 'bat');
        $providerThree = new BasicValueProvider('fizz', 'buzz');
        $collection    = new ProviderCollection(
            $providerOne,
            $providerTwo
        );

        $collection->append($providerThree);

        $this->assertTrue(
            $collection->offsetExists('foo'),
            'Foo was not provided in the provider collection'
        );

        $this->assertEquals(
            'bar',
            $collection->offsetGet('foo'),
            'Foo has bad value in the provider collection'
        );

        $this->assertTrue(
            $collection->offsetExists('baz'),
            'baz was not provided in the provider collection'
        );

        $this->assertEquals(
            'bat',
            $collection->offsetGet('baz'),
            'baz has bad value in the provider collection'
        );

        $this->assertTrue(
            $collection->offsetExists('fizz'),
            'fizz was not provided in the provider collection'
        );

        $this->assertEquals(
            'buzz',
            $collection->offsetGet('fizz'),
            'fizz has bad value in the provider collection'
        );
    }

    /**
     * @test
     */
    public function testItShouldHaveAllTheArrayAccessFunctionality()
    {
        $providerOne   = new BasicValueProvider('foo', 'bar');
        $providerTwo   = new BasicValueProvider('baz', 'bat');
        $providerThree = new BasicValueProvider('fizz', 'buzz');
        $collection    = new ProviderCollection(
            $providerOne,
            $providerTwo
        );

        $collection->append($providerThree);
        $collection->offsetSet('user', 'manchuck');

        $this->assertTrue(
            $collection->offsetExists('user'),
            'user was not provided in the provider collection'
        );

        $this->assertEquals(
            'manchuck',
            $collection->offsetGet('user'),
            'user has bad value in the provider collection'
        );

        $collection->offsetUnset('user');

        $this->assertFalse(
            $collection->offsetExists('user'),
            'user was not removed in the provider collection'
        );

        $collection->offsetUnset('foo');

        $this->assertFalse(
            $collection->offsetExists('foo'),
            'user was not removed in the provider collection'
        );
    }

    /**
     * @test
     */
    public function testItShouldBeAbleToBeIterated()
    {
        $providerOne   = new BasicValueProvider('foo', 'bar');
        $providerTwo   = new BasicValueProvider('baz', 'bat');
        $providerThree = new BasicValueProvider('fizz', 'buzz');
        $collection    = new ProviderCollection(
            $providerOne,
            $providerTwo,
            $providerThree
        );

        $actualData = [];
        foreach ($collection as $key => $value) {
            $actualData[$key] = $value;
        }

        $this->assertEquals(
            [
                'foo' => 'bar',
                'baz' => 'bat',
                'fizz' => 'buzz',
            ],
            $actualData,
            'Provider collection did not iterate correctly'
        );
    }
}
