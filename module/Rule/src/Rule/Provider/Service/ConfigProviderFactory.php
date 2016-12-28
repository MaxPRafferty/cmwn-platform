<?php

namespace Rule\Provider\Service;

use Interop\Container\ContainerInterface;
use Rule\Provider\ProviderInterface;
use Zend\Config\AbstractConfigFactory;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Build an provider from a config string
 *
 * Functions much like the ZF3 AbstractConfigFactory.  This will check the config for a corresponding config key
 *
 * @see AbstractConfigFactory
 */
class ConfigProviderFactory implements AbstractFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (!$container->has('config') || !array_key_exists(self::class, $container->get('config'))) {
            return false;
        }
        $config       = $container->get('config');
        $dependencies = $config[self::class];

        return is_array($dependencies) && array_key_exists($requestedName, $dependencies);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (!$container->has('config')) {
            throw new ServiceNotCreatedException('Cannot find a config array in the container');
        }

        $config = $container->get('config');

        if (!is_array($config)) {
            throw new ServiceNotCreatedException('Config must be an array');
        }

        if (!array_key_exists(self::class, $config)) {
            throw new ServiceNotCreatedException('Cannot find a "' . self::class . '" key in the config array');
        }

        $dependencies = $config[self::class];

        if (!is_array($dependencies)
            || !array_key_exists($requestedName, $dependencies)
            || !is_array($dependencies[$requestedName])
        ) {
            throw new ServiceNotCreatedException('Dependencies config must exist and be an array');
        }

        $serviceDependencies = $dependencies[$requestedName];
        $providerClass = $serviceDependencies['provider_class'] ?? $requestedName;
        unset($serviceDependencies['provider_class']);

        if (!class_exists($providerClass)) {
            throw new ServiceNotCreatedException(sprintf('Cannot find a "%s" at all', $providerClass));
        }

        if (!in_array(ProviderInterface::class, class_implements($providerClass))) {
            throw new ServiceNotCreatedException(
                sprintf(
                    'Cannot create "%s" using "%s" since "%s" is not an provider',
                    $providerClass,
                    self::class,
                    $providerClass
                )
            );
        }

        $arguments = array_map(
            function ($specValue) use (&$container) {
                // Check for a dependency from the SM
                if (is_string($specValue) && $container->has($specValue)) {
                    return $container->get($specValue);
                }

                return $specValue;
            },
            $serviceDependencies
        );

        return new $providerClass(...$arguments);
    }
}
