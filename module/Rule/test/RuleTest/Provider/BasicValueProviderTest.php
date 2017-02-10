<?php

namespace RuleTest\Provider;

use PHPUnit\Framework\TestCase;
use Rule\Provider\BasicValueProvider;

/**
 * Test BasicValueProviderTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BasicValueProviderTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldProvideNameAndValueCorrectly()
    {
        $provider = new BasicValueProvider('foo', 'bar');
        $this->assertEquals(
            'foo',
            $provider->getName(),
            'Basic Value Provider is supplying the wrong name'
        );

        $this->assertEquals(
            'bar',
            $provider->getValue(),
            'Basic value provider is supplying the wrong value'
        );
    }
}
