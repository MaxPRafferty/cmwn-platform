<?php

namespace Rule\Provider\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Creates the provider manager
 */
class ProviderManagerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config       = $container->get('Config');
        $actionConfig = $config['providers'] ?? [];

        return new ProviderManager($container, $actionConfig);
    }
}
