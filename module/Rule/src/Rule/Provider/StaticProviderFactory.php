<?php

namespace Rule\Provider;

use Interop\Container\ContainerInterface;
use Rule\Exception\InvalidArgumentException;
use Rule\Exception\RuntimeException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Factory to build a provider from a container or from a class
 */
class StaticProviderFactory
{
    /**
     * Builds a provider
     *
     * @param ContainerInterface $services
     * @param string $name
     * @param array $options
     *
     * @return ProviderInterface
     */
    public static function build(): ProviderInterface
    {
        $args = func_get_args();
        if (count($args) < 1) {
            throw new InvalidArgumentException(
                sprintf('Provider Factory needs at least one argument to build')
            );
        }

        try {
            if ($args[0] instanceof ContainerInterface) {
                return static::createProviderFromContainer(...$args);
            }
        } catch (ServiceNotFoundException $notFound) {
            unset($args[0]);
        }

        return static::createInvokableProvider(...$args);
    }

    /**
     * Creates a provider by creating a new object
     *
     * If $nameOfClass is not a class, it will create a BasicValueProvider
     *
     * If $nameOfClass is Serializable, then unserialize will be called on object
     * after it is created.
     *
     * @param $nameOfClass
     * @param null $options
     *
     * @throws RuntimeException
     * @return ProviderInterface
     */
    public static function createInvokableProvider($nameOfClass, $options = null): ProviderInterface
    {
        if (!class_exists($nameOfClass)) {
            $options     = [$options, $nameOfClass];
            $nameOfClass = BasicValueProvider::class;
        }

        $provider = is_array($options)
            ? new $nameOfClass(...$options)
            : new $nameOfClass();

        if (!$provider instanceof ProviderInterface) {
            throw new RuntimeException(
                sprintf('%s is not a provider', $nameOfClass)
            );
        }

        if ($provider instanceof \Serializable) {
            $provider->unserialize($options);
        }

        return $provider;
    }

    /**
     * Creates the provider from a container
     *
     * if the provider is serializable, then unserialize will be called on the object
     * after it is pulled out from the container
     *
     * @param ContainerInterface $services
     * @param $name
     * @param array|string $options
     *
     * @throws ServiceNotFoundException
     * @throws RuntimeException
     * @return ProviderInterface
     */
    public static function createProviderFromContainer(
        ContainerInterface $services,
        $name,
        $options = ""
    ): ProviderInterface {
        if (!$services->has($name)) {
            // TODO change this to interop instead of zf3
            throw new ServiceNotFoundException();
        }

        $provider = $services->get($name);
        if (!$provider instanceof ProviderInterface) {
            throw new RuntimeException(
                sprintf('The Provider %s is not a valid provider', $name)
            );
        }

        if ($provider instanceof \Serializable) {
            $provider->unserialize($options);
        }

        return $provider;
    }
}
