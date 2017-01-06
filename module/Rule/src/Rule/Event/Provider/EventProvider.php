<?php

namespace Rule\Event\Provider;

use Zend\EventManager\EventInterface;

/**
 * Provides the event
 */
class EventProvider extends AbstractEventProvider
{
    /**
     * @inheritDoc
     */
    public function getValue(): EventInterface
    {
        return $this->getEvent();
    }
}
