<?php

namespace Rule\Provider;

use Interop\Container\ContainerInterface;

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

            $collection->append(StaticProviderFactory::build($services, $item, $key));
        });

        return $collection;
    }
}
