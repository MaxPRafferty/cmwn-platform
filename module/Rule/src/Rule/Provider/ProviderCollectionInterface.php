<?php

namespace Rule\Provider;

/**
 * Interface ProviderCollectionInterface
 */
interface ProviderCollectionInterface extends \IteratorAggregate, \ArrayAccess
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
