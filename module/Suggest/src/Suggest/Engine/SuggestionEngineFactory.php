<?php

namespace Suggest\Engine;

use Interop\Container\ContainerInterface;
use Suggest\Filter\FilterCollection;
use Suggest\Rule\RuleCollection;
use Suggest\Service\SuggestedServiceInterface;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class SuggestionEngineFactory
 */
class SuggestionEngineFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SuggestionEngine(
            $container->get(RuleCollection::class),
            $container->get(FilterCollection::class),
            $container->get(SuggestedServiceInterface::class),
            $container->get(UserServiceInterface::class)
        );
    }
}
