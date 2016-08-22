<?php

namespace Suggest\Engine;

use Suggest\Service\SuggestedService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SuggestionEngineFactory
 * @package Suggest\Engine
 */
class SuggestionEngineFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return SuggestionEngine
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $suggestedService = $serviceLocator->get(SuggestedService::class);

        $config = $serviceLocator->get('config');
        $config = isset($config['suggestion-engine']) ? $config['suggestion-engine'] : [];

        return new SuggestionEngine($serviceLocator, $suggestedService, $config);
    }
}
