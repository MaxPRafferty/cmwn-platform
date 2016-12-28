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

    /**
     * Used to retrieve a provider
     *
     * @param $name
     *
     * @return ProviderInterface
     */
    public function getProvider($name): ProviderInterface;
}
