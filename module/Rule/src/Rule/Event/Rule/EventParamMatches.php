<?php

namespace Rule\Event\Rule;

use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Zend\EventManager\EventInterface;

/**
 * checks if the event param matches a given value
 */
class EventParamMatches extends AbstractEventRule implements RuleInterface
{
    protected $eventParam;

    /**
     * EventParamMatches constructor.
     * @param string $eventParam
     * @param $expectedValue
     * @param $eventProviderName
     */
    public function __construct($eventParam, $expectedValue, $eventProviderName = 'event')
    {
        parent::__construct($eventProviderName, $expectedValue);
        $this->eventParam = $eventParam;
    }

    /**
     * @param RuleItemInterface $item
     * @return bool
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        $event = $item->getParam($this->eventProviderName);

        if (!$event instanceof EventInterface) {
            return false;
        }

        return $event->getParam($this->eventParam) === $this->expectedValue;
    }
}
