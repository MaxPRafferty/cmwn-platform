<?php

namespace Rule\Utils;

use Interop\Container\ContainerInterface;
use Rule\Action\Service\BuildActionFromConfigFactory;
use Rule\Provider\Service\BuildProvderFromConfigFactory;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Functions like the AbstractConfigBuilder in Zf3
 *
 * @see ConfigAbstractFactory
 * @see BuildActionFromConfigFactory
 * @see BuildProvderFromConfigFactory
 */
abstract class AbstractConfigBuilderFactory implements AbstractFactoryInterface
{
    /**
     * @var string optional key to check in the config to specify the name of the object to create
     */
    protected $itemClassKey;

    /**
     * @var string the type the instance needs to be
     */
    protected $instanceOf;

    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (!$container->has('config') || !array_key_exists(static::class, $container->get('config'))) {
            return false;
        }
        $config       = $container->get('config');
        $dependencies = $config[static::class];

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

        if (!array_key_exists(static::class, $config)) {
            throw new ServiceNotCreatedException('Cannot find a "' . static::class . '" key in the config array');
        }

        $dependencies = $config[static::class];

        if (!is_array($dependencies)
            || !array_key_exists($requestedName, $dependencies)
            || !is_array($dependencies[$requestedName])
        ) {
            throw new ServiceNotCreatedException('Dependencies config must exist and be an array');
        }

        $serviceDependencies = $dependencies[$requestedName];
        $itemClass           = $serviceDependencies[$this->itemClassKey] ?? $requestedName;
        unset($serviceDependencies[$this->itemClassKey]);

        if (!class_exists($itemClass)) {
            throw new ServiceNotCreatedException(sprintf('Cannot find a "%s" at all', $itemClass));
        }

        if (!in_array($this->instanceOf, class_implements($itemClass))) {
            throw new ServiceNotCreatedException(
                sprintf(
                    'Cannot create "%s" using "%s" since "%s" is not an instance of %s',
                    $itemClass,
                    BuildProviderFromConfigFactory::class,
                    $itemClass,
                    $this->instanceOf
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

        return new $itemClass(...array_values($arguments));
    }
}
