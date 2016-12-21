<?php

namespace Rule\Item;

use Zend\EventManager\EventInterface;

/**
 * A Rule item is an expanded event that is use to satisfy rules
 */
interface RuleItemInterface
{
    /**
     * Gets an item parameter
     *
     * @param string $param
     * @param null $default default value to return if $param is not set
     *
     * @return mixed
     */
    public function getParam(string $param, $default = null);

    /**
     * Passes the event from the rules engine into the item
     *
     * @param EventInterface $event
     *
     * @return RuleItemInterface
     */
    public function setEvent(EventInterface $event): RuleItemInterface;
}
