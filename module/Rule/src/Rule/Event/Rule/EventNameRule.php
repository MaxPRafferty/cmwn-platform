<?php

namespace Rule\Event\Rule;

use Rule\Item\RuleItemInterface;
use Zend\EventManager\EventInterface;

/**
 * This is satisfied when the event name matches what is expected
 */
class EventNameRule extends AbstractEventRule
{
    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        $event     = $item->getParam($this->eventProviderName);
        $eventName = $event instanceof EventInterface ? $event->getName() : $event;

        if ($eventName === $this->expectedValue) {
            $this->timesSatisfied++;

            return true;
        }

        return false;
    }
}
