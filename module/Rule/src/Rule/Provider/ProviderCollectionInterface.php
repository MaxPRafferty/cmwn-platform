<?php

namespace Rule\Provider;

use Rule\Item\RuleItemInterface;

/**
 * Interface ProviderCollectionInterface
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
