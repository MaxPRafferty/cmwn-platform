<?php

namespace Rule\Event\Provider;

/**
 * Provides the event
 */
class EventProvider extends AbstractEventProvider
{
    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->getEvent();
    }
}
