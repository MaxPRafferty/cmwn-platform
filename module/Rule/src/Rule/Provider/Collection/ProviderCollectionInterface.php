<?php

namespace Rule\Provider\Collection;

use Rule\Item\RuleItemInterface;
use Rule\Provider\ProviderInterface;

/**
 * Defines a collection of providers
 */
interface ProviderCollectionInterface extends \IteratorAggregate, \ArrayAccess, RuleItemInterface
{
    /**
     * Adds a provider to the collection
     *
     * @param ProviderInterface $provider
     *
     * @return ProviderCollectionInterface
     */
    public function append(ProviderInterface $provider): ProviderCollectionInterface;
}
