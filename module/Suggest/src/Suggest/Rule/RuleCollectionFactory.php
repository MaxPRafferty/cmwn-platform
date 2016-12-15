<?php

namespace Suggest\Rule;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RuleCollectionFactory
 */
class RuleCollectionFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config           = $serviceLocator->get('config');
        $suggestionConfig = isset($config['suggestion-engine']) ? $config['suggestion-engine'] : [];
        $rulesConfig      = isset($suggestionConfig['rules']) ? $suggestionConfig['rules'] : [];

        return new RuleCollection($serviceLocator, $rulesConfig);
    }
}
