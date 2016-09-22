<?php

namespace SuggestCron\Controller;

use Job\Service\JobServiceInterface;
use Suggest\Engine\SuggestionEngine;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SuggestCronControllerFactory
 * @package SuggestCron\Controller
 */
class SuggestCronControllerFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator instanceof ServiceLocatorAwareInterface
            ? $serviceLocator->getServiceLocator()
            : $serviceLocator;
        $userService = $serviceLocator->get(UserServiceInterface::class);
        $suggestionEngine = $serviceLocator->get(SuggestionEngine::class);
        $authService = $serviceLocator->get('authentication');
        $jobService = $serviceLocator->get(JobServiceInterface::class);

        return new SuggestCronController($userService, $suggestionEngine, $authService, $jobService);
    }
}
