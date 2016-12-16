<?php

namespace Rule\Item;

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
}
