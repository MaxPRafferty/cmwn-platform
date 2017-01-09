<?php

namespace Rule\Provider\Collection;

/**
 * Defines a class that can handle Provider Collections
 */
interface ProviderCollectionAwareInterface
{
    /**
     * Sets the provider collection
     *
     * @param ProviderCollectionInterface $collection
     */
    public function setProviderCollection(ProviderCollectionInterface $collection);

    /**
     * Gets the provide collection
     *
     * @return ProviderCollectionInterface
     */
    public function getProviderCollection(): ProviderCollectionInterface;
}
