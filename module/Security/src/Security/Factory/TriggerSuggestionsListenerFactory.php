<?php

namespace Security\Factory;

use Security\Listeners\TriggerSuggestionsListener;
use Suggest\Engine\SuggestionEngine;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class TriggerSuggestionsListenerFactory
 * @package Security\Factory
 */
class TriggerSuggestionsListenerFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $suggestionEngine = $serviceLocator->get(SuggestionEngine::class);

        return new TriggerSuggestionsListener($suggestionEngine);
    }
}
