<?php

namespace Suggest\Filter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FilterCollectionFactory
 */
class FilterCollectionFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config           = $serviceLocator->get('config');
        $suggestionConfig = isset($config['suggestion-engine']) ? $config['suggestion-engine'] : [];
        $filtersConfig    = isset($suggestionConfig['filters']) ? $suggestionConfig['filters'] : [];

        return new FilterCollection($serviceLocator, $filtersConfig);
    }
}
