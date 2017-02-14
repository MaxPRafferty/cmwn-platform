<?php

namespace RuleTest\Event\Provider;

use PHPUnit\Framework\TestCase as TestCase;
use Rule\Event\Provider\FromEventTargetProvider;
use Zend\EventManager\Event;

/**
 * Test FromEventTargetProviderTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FromEventTargetProviderTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldReturnTarget()
    {
        $target = new \stdClass();
        $event  = new Event();
        $event->setTarget($target);

        $provider = new FromEventTargetProvider('foo');
        $provider->setEvent($event);

        $this->assertSame(
            'foo',
            $provider->getName(),
            'FromEventTargetProvider did not return the correct name'
        );

        $this->assertSame(
            $target,
            $provider->getValue(),
            'FromEventTargetProvider did not return the correct value'
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnNullWhenThereIsNoTarget()
    {
        $event    = new Event();
        $provider = new FromEventTargetProvider('foo');
        $provider->setEvent($event);

        $this->assertSame(
            'foo',
            $provider->getName(),
            'FromEventTargetProvider did not return the correct name'
        );

        $this->assertNull(
            $provider->getValue(),
            'FromEventTargetProvider did not return the correct value'
        );
    }
}
