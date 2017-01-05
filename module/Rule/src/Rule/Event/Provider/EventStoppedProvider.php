<?php

namespace Rule\Event\Provider;

/**
 * Provides the value from the value being stopped or not
 */
class EventStoppedProvider extends AbstractEventProvider
{
    /**
     * @inheritDoc
     */
    public function getValue(): bool
    {
        return $this->getEvent()->propagationIsStopped();
    }
}
