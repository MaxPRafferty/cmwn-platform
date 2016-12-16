<?php

namespace Rule\Provider;

use Zend\EventManager\EventInterface;

/**
 * A Rule Provider will supply data requested from rules into the Rule Item
 */
interface ProviderInterface
{
    /**
     * Gets the name of the value this provides
     *
     * @param EventInterface $event
     *
     * @return string
     */
    public function getName(EventInterface $event = null): string;

    /**
     * Gets the value this provides
     *
     * Event is passed just in case the parameter is inside the event
     *
     * @param EventInterface $event
     * @return mixed
     */
    public function getValue(EventInterface $event = null);
}
