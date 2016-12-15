<?php

namespace Rule;

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
     * Gets all the data for the Item
     *
     * @return array
     */
    public function getArrayCopy(): array;

    /**
     * Sets the data for the object
     *
     * @param array $data
     */
    public function exchangeArray(array $data);
}
