<?php

namespace Suggest\Listener;

use Job\Service\JobServiceInterface;
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
        $jobService = $serviceLocator->get(JobServiceInterface::class);

        return new TriggerSuggestionsListener($suggestionEngine, $jobService);
    }
}