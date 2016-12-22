<?php

namespace Rule\Action;

use Interop\Container\ContainerInterface;
use Rule\Exception\InvalidArgumentException;
use Rule\Exception\RuntimeException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * A Static factory that will create an action
 */
class StaticActionFactory
{
    /**
     * Builds an action
     *
     * The action will either be pulled from the Container or constructed from a class
     *
     * @return ActionInterface
     * @internal param ContainerInterface $services
     * @internal param string $className
     * @internal param array $options
     */
    public static function build(): ActionInterface
    {
        $args = func_get_args();
        if (count($args) < 1) {
            throw new InvalidArgumentException(
                sprintf('Provider Factory needs at least one argument to build')
            );
        }

        try {
            if ($args[0] instanceof ContainerInterface) {
                return static::createActionFromContainer(...$args);
            }
        } catch (ServiceNotFoundException $notFound) {
            unset($args[0]);
        }

        return static::createInvokableAction(...$args);
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
     * @throws InvalidArgumentException
     * @return ActionInterface
     */
    public static function createInvokableAction($nameOfClass, $options = null): ActionInterface
    {
        if (is_array($nameOfClass)) {
            $options = null === $options && isset($nameOfClass['options'])
                ? $nameOfClass['options']
                : $options;

            $nameOfClass = $nameOfClass['name'] ?? null;
        }

        if (!class_exists($nameOfClass)) {
            throw new InvalidArgumentException(
                sprintf('Class %s is not found', $nameOfClass)
            );
        }

        $provider = is_array($options)
            ? new $nameOfClass(...$options)
            : new $nameOfClass();

        if (!$provider instanceof ActionInterface) {
            throw new RuntimeException(
                sprintf('The class %s is not an action', $nameOfClass)
            );
        }

        if ($provider instanceof \Serializable) {
            $provider->unserialize($options);
        }

        return $provider;
    }

    /**
     * Creates the action from a container
     *
     * if the action is serializable, then unserialize will be called on the object
     * after it is pulled out from the container
     *
     * @param ContainerInterface $services
     * @param $name
     * @param array|string $options
     *
     * @throws ServiceNotFoundException
     * @throws RuntimeException
     * @return ActionInterface
     */
    public static function createActionFromContainer(
        ContainerInterface $services,
        $name,
        $options = ""
    ): ActionInterface {
        if (!$services->has($name)) {
            // TODO change this to interop instead of zf3
            throw new ServiceNotFoundException();
        }

        $provider = $services->get($name);
        if (!$provider instanceof ActionInterface) {
            throw new RuntimeException(
                sprintf('The service %s is not a valid action', $name)
            );
        }

        if ($provider instanceof \Serializable) {
            $provider->unserialize($options);
        }

        return $provider;
    }
}
