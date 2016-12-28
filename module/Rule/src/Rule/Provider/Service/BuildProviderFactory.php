<?php

namespace Rule\Provider\Service;

use Interop\Container\ContainerInterface;
use Rule\Provider\BasicValueProvider;
use Rule\Provider\ProviderInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Builds a Basic value provider from an array
 */
class BuildProviderFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (!is_array($options) || empty($options)) {
            throw new ServiceNotCreatedException(
                sprintf(
                    '%s cannot be used to get a basic value provider.  Use build instead',
                    self::class
                )
            );
        }


        if (!class_exists($requestedName) || !in_array(ProviderInterface::class, class_implements($requestedName))) {
            throw new ServiceNotCreatedException(
                sprintf(
                    '%s cannot build %s as %s is not a provider',
                    self::class,
                    $requestedName,
                    $requestedName
                )
            );
        }

        return new BasicValueProvider(...array_values($options));
    }
}
