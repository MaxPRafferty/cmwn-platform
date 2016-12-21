<?php

namespace Suggest\Listener;

use Suggest\Service\SuggestedServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DeleteSuggestionListenerFactory
 * @package Suggest\Listener
 */
class DeleteSuggestionListenerFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new DeleteSuggestionListener($serviceLocator->get(SuggestedServiceInterface::class));
    }
}
