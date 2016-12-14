<?php

namespace Suggest\Engine;

use Suggest\Filter\FilterCollection;
use Suggest\Rule\RuleCollection;
use Suggest\Service\SuggestedServiceInterface;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SuggestionEngineFactory
 * @package Suggest\Engine
 */
class SuggestionEngineFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new SuggestionEngine(
            $serviceLocator->get(RuleCollection::class),
            $serviceLocator->get(FilterCollection::class),
            $serviceLocator->get(SuggestedServiceInterface::class),
            $serviceLocator->get(UserServiceInterface::class)
        );
    }
}
