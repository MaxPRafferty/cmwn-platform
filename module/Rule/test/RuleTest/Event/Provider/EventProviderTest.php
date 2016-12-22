<?php

namespace RuleTest\Event\Provider;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Event\Provider\EventProvider;
use Zend\EventManager\Event;

/**
 * Test EventProviderTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EventProviderTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldProvideEvent()
    {
        $event    = new Event();
        $provider = new EventProvider('foo');
        $provider->setEvent($event);
        $this->assertSame(
            'foo',
            $provider->getName(),
            'EventProvider did not return the correct name'
        );

        $this->assertSame(
            $event,
            $provider->getValue(),
            'EventProvider did not return the event'
        );
    }
}
