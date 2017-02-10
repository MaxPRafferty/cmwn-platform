<?php

namespace RuleTest\Event\Provider;

use PHPUnit\Framework\TestCase;
use Rule\Event\Provider\EventStoppedProvider;
use Zend\EventManager\Event;

/**
 * Test EventStoppedProviderTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EventStoppedProviderTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldReturnStoppedStatus()
    {
        $event = new Event();
        $event->stopPropagation(true);

        $provider = new EventStoppedProvider('foo');
        $provider->setEvent($event);
        $this->assertSame(
            'foo',
            $provider->getName(),
            'EventStoppedProvider did not return correct name'
        );

        $this->assertTrue(
            $provider->getValue(),
            'EventStoppedProvider did not return correct value'
        );
    }
}
