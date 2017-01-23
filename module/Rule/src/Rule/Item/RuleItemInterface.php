<?php

namespace Rule\Item;

use Rule\Provider\ProviderInterface;

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
     * Appends a new provider
     *
     * This will allow rules and actions to provide data
     *
     * @param ProviderInterface $provider
     *
     * @return RuleItemInterface
     */
    public function append(ProviderInterface $provider): RuleItemInterface;
}
