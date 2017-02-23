<?php

namespace RuleTest\Event\Rule;

use Rule\Event\Provider\EventProvider;
use Rule\Event\Rule\EventParamMatches;
use Rule\Item\BasicRuleItem;
use Zend\EventManager\Event;

/**
 * Unit test for EventParamMatchesRule
 */
class EventParamMatchesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenEventParamMatchesWithExpectedValue()
    {
        $event    = new Event();
        $event->setParam('foo', 'bar');
        $rule     = new EventParamMatches('foo', 'bar');
        $provider = new EventProvider('event');
        $provider->setEvent($event);

        $item = new BasicRuleItem($provider);

        $this->assertTrue(
            $rule->isSatisfiedBy($item),
            'Rule was not satisfied when event param matches'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenEventParamMatchesWithExpectedValue()
    {
        $event    = new Event();
        $event->setParam('foo', 'bar');
        $rule     = new EventParamMatches('foo', 'baz');
        $provider = new EventProvider('event');
        $provider->setEvent($event);

        $item = new BasicRuleItem($provider);

        $this->assertFalse(
            $rule->isSatisfiedBy($item),
            'Rule was satisfied when event param does not match'
        );
    }
}
