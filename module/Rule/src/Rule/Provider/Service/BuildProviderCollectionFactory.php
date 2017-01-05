<?php

namespace Rule\Provider\Service;

use Rule\Provider\Collection\ProviderCollection;
use Rule\Provider\Collection\ProviderCollectionInterface;
use Rule\Provider\ProviderInterface;
use Rule\Utils\AbstractCollectionBuilder;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * A Factory that will build a collection of providers
 *
 * Config options:
 *
 * // Define an optional provider collection (optional will use $requestedName otherwise)
 * 'provider_collection_class' => 'Some\Provider\Collection',
 *
 * // Defines the providers to add to the collection
 * 'providers' => [
 *      // This will append the provider
 *      new BasicValueProvider('foo', 'bar'),
 *
 *      // This will GET the rule from the manager
 *      'Manchuck\Provider',
 *      Manchuck\Provider::class,
 *
 *     // This will build a provider
 *     [
 *          // Name of the provider to build
 *          'name'    => 'Some/Provider/To/Build',
 *
 *          // Options passed into the builder
 *          'options' => ['foo', 'bar'],
 *     ]
 * ]
 *
 * @see ProviderCollection
 */
class BuildProviderCollectionFactory extends AbstractCollectionBuilder implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    protected $collectionClassKey = 'provider_collection_class';

    /**
     * @inheritDoc
     */
    protected $collectionItemsKey = 'providers';

    /**
     * @inheritDoc
     */
    protected $collectionInstanceOf = ProviderCollectionInterface::class;

    /**
     * @inheritDoc
     */
    protected $itemInstanceOf = ProviderInterface::class;

    /**
     * @inheritDoc
     */
    protected $pluginManagerName = ProviderManager::class;
}
