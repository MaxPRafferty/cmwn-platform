<?php

namespace Rule\Engine\Service;

use Interop\Container\ContainerInterface;
use Rule\Action\Service\ActionManager;
use Rule\Engine\Engine;
use Rule\Provider\Service\ProviderManager;
use Rule\Rule\Service\RuleManager;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class EngineFactory
 */
class EngineFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $specificationManger = $container->get(SpecificationManager::class);
        return new Engine(
            $container->get(SharedEventManagerInterface::class),
            $container->get(ActionManager::class),
            $container->get(RuleManager::class),
            $container->get(ProviderManager::class),
            $specificationManger->get('AllSpecifications')
        );
    }
}
