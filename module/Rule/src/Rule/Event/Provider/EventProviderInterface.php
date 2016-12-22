<?php

namespace Rule\Event\Provider;

use Rule\Provider\ProviderInterface;
use Zend\EventManager\EventInterface;

/**
 * This type of provider will get the data from an event
 */
interface EventProviderInterface extends ProviderInterface
{
    /**
     * Sets the event for the provider
     *
     * @param EventInterface $event
     */
    public function setEvent(EventInterface $event);

    /**
     * Gets the event for the provider
     *
     * @return EventInterface
     */
    public function getEvent(): EventInterface;
}
