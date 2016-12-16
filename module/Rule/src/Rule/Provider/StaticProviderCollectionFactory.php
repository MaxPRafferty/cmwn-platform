<?php

namespace Rule\Provider;

use Interop\Container\ContainerInterface;
use Rule\Exception\RuntimeException;

/**
 * Class StaticProviderCollectionFactory
 */
class StaticProviderCollectionFactory
{
    /**
     * @param ContainerInterface $services
     * @param array $items
     *
     * @return ProviderCollectionInterface
     */
    public static function build(ContainerInterface $services, array $items): ProviderCollectionInterface
    {
        $collection = new ProviderCollection();
        array_walk($items, function ($item, $key) use (&$services, &$collection) {
            // if we have a provider then bob's your uncle
            if ($item instanceof ProviderInterface) {
                $collection->append($item);
                return;
            }

            // Get the provider from the container
            if ($services->has($item)) {
                $collection->append(static::createProviderFromContainer($services, $item));
                return;
            }

            // If the name is a class then lets create the class
            if (class_exists($item)) {
                $collection->append(static::createProviderFromClass($item));
                return;
            }

            // otherwise just make it a basic value provider
            $collection->append(new BasicValueProvider($key, $item));
        });

        return $collection;
    }

    /**
     * Creates a provider from the class
     *
     * @param $name
     *
     * @return ProviderInterface
     */
    protected static function createProviderFromClass($name): ProviderInterface
    {
        $provider = new $name();
        if (!$provider instanceof ProviderInterface) {
            throw new RuntimeException(
                sprintf('The Provider %s is not a valid provider', $name)
            );
        }

        return $provider;
    }

    /**
     * Creates the provider from a container
     *
     * @param ContainerInterface $services
     * @param $name
     *
     * @return ProviderInterface
     */
    protected static function createProviderFromContainer(ContainerInterface $services, $name): ProviderInterface
    {
        $provider = $services->get($name);
        if (!$provider instanceof ProviderInterface) {
            throw new RuntimeException(
                sprintf('The Provider %s is not a valid provider', $name)
            );
        }

        return $provider;
    }
}
