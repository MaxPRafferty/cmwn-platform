<?php

namespace Suggest\Listener;

use Job\Service\JobServiceInterface;
use Suggest\Engine\SuggestionEngine;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class TriggerSuggestionsListenerFactory
 *
 * @package Security\Factory
 */
class TriggerSuggestionsListenerFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new TriggerSuggestionsListener(
            $serviceLocator->get(SuggestionEngine::class),
            $serviceLocator->get(JobServiceInterface::class)
        );
    }
}
