<?php

namespace RuleTest\Event\Provider;

use PHPUnit\Framework\TestCase as TestCase;
use Rule\Event\Provider\FromEventParamProvider;
use Zend\EventManager\Event;

/**
 * Test FromEventParamProviderTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FromEventParamProviderTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldGetValueFromEventParam()
    {
        $event = new Event();
        $event->setParam('foo', 'bar');

        $provider = new FromEventParamProvider('foo');
        $provider->setEvent($event);
        $this->assertSame(
            'foo',
            $provider->getName(),
            'FromEventParamProvider did not set the correct name'
        );

        $this->assertSame(
            'bar',
            $provider->getValue(),
            'FromEventParamProvider did not set the correct value'
        );
    }

    /**
     * @test
     */
    public function testItShouldGetValueFromEventParamWithDifferentName()
    {
        $event = new Event();
        $event->setParam('foo', 'bar');

        $provider = new FromEventParamProvider('baz', 'foo');
        $provider->setEvent($event);
        $this->assertSame(
            'baz',
            $provider->getName(),
            'FromEventParamProvider did not set the correct name'
        );

        $this->assertSame(
            'bar',
            $provider->getValue(),
            'FromEventParamProvider did not set the correct value'
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnNullWhenWhenEventParamNotSet()
    {
        $event    = new Event();
        $provider = new FromEventParamProvider('baz', 'foo');
        $provider->setEvent($event);
        $this->assertSame(
            'baz',
            $provider->getName(),
            'FromEventParamProvider did not set the correct name'
        );

        $this->assertNull(
            $provider->getValue(),
            'FromEventParamProvider did not set the correct value'
        );
    }
}
