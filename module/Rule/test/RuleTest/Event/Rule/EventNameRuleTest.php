<?php

namespace RuleTest\Event\Rule;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Event\Provider\EventProvider;
use Rule\Event\Rule\EventNameRule;
use Rule\Item\BasicRuleItem;
use Zend\EventManager\Event;

/**
 * Test EventNameRuleTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EventNameRuleTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenEventNameMatchesWithEvent()
    {
        $event    = new Event('foo.bar');
        $rule     = new EventNameRule('event_name', 'foo.bar');
        $provider = new EventProvider('event_name');
        $provider->setEvent($event);

        $item = new BasicRuleItem($provider);

        $this->assertTrue(
            $rule->isSatisfiedBy($item),
            'Rule was not satisfied when event name matches'
        );
    }
}
