<?php

namespace Suggest\Filter;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FilterCollectionFactory
 */
class FilterCollectionFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config           = $container->get('config');
        $suggestionConfig = isset($config['suggestion-engine']) ? $config['suggestion-engine'] : [];
        $filtersConfig    = isset($suggestionConfig['filters']) ? $suggestionConfig['filters'] : [];

        return new FilterCollection(
            $container,
            $filtersConfig
        );
    }
}
