<?php

namespace Suggest\Listener;

use Interop\Container\ContainerInterface;
use Job\Service\JobServiceInterface;
use Suggest\Engine\SuggestionEngine;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class TriggerSuggestionsListenerFactory
 */
class TriggerSuggestionsListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new TriggerSuggestionsListener(
            $container->get(SuggestionEngine::class),
            $container->get(JobServiceInterface::class)
        );
    }
}
