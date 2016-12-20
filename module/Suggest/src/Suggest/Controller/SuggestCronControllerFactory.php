<?php

namespace Suggest\Controller;

use Interop\Container\ContainerInterface;
use Job\Service\JobServiceInterface;
use Suggest\Engine\SuggestionEngine;
use User\Service\UserServiceInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class SuggestCronControllerFactory
 */
class SuggestCronControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SuggestCronController(
            $container->get(UserServiceInterface::class),
            $container->get(SuggestionEngine::class),
            $container->get(AuthenticationServiceInterface::class),
            $container->get(JobServiceInterface::class)
        );
    }

}
