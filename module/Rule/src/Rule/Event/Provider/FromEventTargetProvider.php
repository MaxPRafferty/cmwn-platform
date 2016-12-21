<?php

namespace Rule\Event\Provider;

/**
 * This provides the value from the event target
 */
class FromEventTargetProvider extends AbstractEventProvider
{
    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->getEvent()->getTarget();
    }
}
