<?php


namespace Suggest\Controller;

use Suggest\Engine\SuggestionEngine;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SuggestionControllerFactory
 * @package Suggest\Controller
 */
class SuggestionControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return SuggestionController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator instanceof ServiceLocatorAwareInterface
            ? $serviceLocator->getServiceLocator()
            : $serviceLocator;
        $suggestionEngine = $serviceLocator->get(SuggestionEngine::class);
        $userService = $serviceLocator->get(UserServiceInterface::class);
        return new SuggestionController($suggestionEngine, $userService);
    }
}
