<?php

namespace Rule\Event\Rule;

use Rule\Item\RuleItemInterface;
use Rule\Utils\ProviderTypeTrait;
use Zend\EventManager\EventInterface;

/**
 * This is satisfied when the event name matches what is expected
 */
class EventNameRule extends AbstractEventRule
{
    use ProviderTypeTrait;

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        $event     = $item->getParam($this->eventProviderName);
        static::checkValueType($event, EventInterface::class);

        if ($event->getName() === $this->expectedValue) {
            $this->timesSatisfied++;

            return true;
        }

        return false;
    }
}
