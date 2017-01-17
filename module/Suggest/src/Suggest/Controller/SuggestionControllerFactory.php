<?php

namespace Suggest\Controller;

use Interop\Container\ContainerInterface;
use Suggest\Engine\SuggestionEngine;
use Zend\ServiceManager\Factory\FactoryInterface;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class SuggestionControllerFactory
 */
class SuggestionControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SuggestionController(
            $container->get(SuggestionEngine::class),
            $container->get(UserServiceInterface::class)
        );
    }
}
