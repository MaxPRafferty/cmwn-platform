<?php

namespace RuleTest\Event\Provider;

use PHPUnit\Framework\TestCase;
use Rule\Event\Provider\FromEventNameProvider;
use Zend\EventManager\Event;

/**
 * Test FromEventNameProviderTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FromEventNameProviderTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldReturnTheNameOfTheEvent()
    {
        $event = new Event();
        $event->setName('foo.bar');

        $provider = new FromEventNameProvider('event_name');
        $provider->setEvent($event);
        $this->assertSame(
            'event_name',
            $provider->getName(),
            'Event Provider did not set the correct name'
        );

        $this->assertSame(
            'foo.bar',
            $provider->getValue(),
            'Event Provider did not set the correct value'
        );
    }
}
