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
    public function getValue(): string
    {
        return $this->getEvent()->getName();
    }
}
