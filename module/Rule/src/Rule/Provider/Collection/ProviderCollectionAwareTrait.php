<?php

namespace Rule\Provider\Collection;

/**
 * Class ProviderCollectionAwareTrait
 */
trait ProviderCollectionAwareTrait
{
    /**
     * @var ProviderCollectionInterface
     */
    protected $providerCollection;

    /**
     * Sets the provider collection
     *
     * @param ProviderCollectionInterface $collection
     *
     * @return $this
     */
    public function setProviderCollection(ProviderCollectionInterface $collection)
    {
        $this->providerCollection = $collection;
        return $this;
    }

    /**
     * Gets the provide collection
     *
     * @return ProviderCollectionInterface
     */
    public function getProviderCollection(): ProviderCollectionInterface
    {
        return $this->providerCollection;
    }
}
