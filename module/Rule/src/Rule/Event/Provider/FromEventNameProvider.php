<?php

namespace Rule\Event\Provider;

/**
 * Provides the value from the event name
 */
class FromEventNameProvider extends AbstractEventProvider
{
    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->getEvent()->getName();
    }
}
