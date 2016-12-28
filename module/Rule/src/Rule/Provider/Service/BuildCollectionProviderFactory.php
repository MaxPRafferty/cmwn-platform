<?php

namespace Rule\Provider\Service;

use Interop\Container\ContainerInterface;
use Rule\Provider\Collection\ProviderCollectionInterface;
use Rule\Provider\ProviderInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class CollectionProviderFactory
 */
class BuildCollectionProviderFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = is_array($options) ? $options : [];
        /** @var ProviderManager $providerManager */
        $collectionClass    = $options['provider_collection_class'] ?? $requestedName;
        $providerCollection = new $collectionClass();

        if (!$providerCollection instanceof ProviderCollectionInterface) {
            throw new ServiceNotCreatedException(
                sprintf('%s is not a valid provider collection', $collectionClass)
            );
        }

        unset($options['provider_collection_class']);
        $providerManager     = $container->get(ProviderManager::class);
        $collectionProviders = $options['providers'] ?? $options;

        array_walk($collectionProviders, function ($collectionSpec) use (&$providerCollection, &$providerManager) {
            $providerSpec = is_array($collectionSpec) && isset($collectionSpec['provider'])
                ? $collectionSpec['provider']
                : $collectionSpec;

            switch (true) {
                // Get the provider from the manager
                case is_string($providerSpec):
                    $providerSpec = $providerManager->get($providerSpec);
                // we want to fall through

                // Append a built provider
                case ($providerSpec instanceof ProviderInterface):
                    $providerCollection->append($providerSpec);
                    break;

                // Build the provider
                case is_array($providerSpec):
                    $providerName    = $providerSpec['name'] ?? null;
                    $providerOptions = $providerSpec['options'] ?? [];
                    $providerCollection->append(
                        $providerManager->build($providerName, $providerOptions)
                    );
                    break;

                default:
                    throw new ServiceNotCreatedException('Invalid Provider spec type');
            }
        });

        return $providerCollection;
    }
}
