<?php

namespace Rule;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\NotFoundException;
use Rule\Exception\InvalidArgumentException;
use Rule\Exception\RuntimeException;
/**
 * Class StaticRuleFactory
 */
class StaticRuleFactory
{
    /**
     * @param ContainerInterface $services
     * @param $name
     * @param array|string $options
     *
     * @throws RuntimeException
     * @return RuleInterface
     */
    public static function buildRuleFromContainer(
        ContainerInterface $services,
        $name,
        $options = ""
    ): RuleInterface {
        $rule = $services->get($name);
        if (!$rule instanceof RuleInterface) {
            throw new RuntimeException(
                sprintf('%s is not a rule', $name)
            );
        }

        if ($rule instanceof \Serializable) {
            $rule->unserialize($options);
        }

        return $rule;
    }

    /**
     * Builds a rule by invoking the class and passing in arguments
     *
     * @param $nameOfClass
     * @param null $options
     *
     * @return RuleInterface
     */
    public static function buildRuleFromClass($nameOfClass, $options = null): RuleInterface
    {
        if (!class_exists($nameOfClass)) {
            throw new InvalidArgumentException(
                sprintf('Class %s not found', $nameOfClass)
            );
        }

        $rule = is_array($options)
            ? new $nameOfClass(...$options)
            : new $nameOfClass();

        if (!$rule instanceof RuleInterface) {
            throw new RuntimeException(
                sprintf('%s is not a rule', $nameOfClass)
            );
        }

        if ($rule instanceof \Serializable) {
            $rule->unserialize($options);
        }

        return $rule;
    }

    /**
     * Builds a rule using either by the class or from the container
     *
     * @param $paramOne ContainerInterface|string Container or classname
     * @param $paramTwo string|array Options or class name to pass in
     * @param $parmaThree array The options
     *
     * @return RuleInterface
     */
    public static function buildRule(): RuleInterface
    {
        $args = func_get_args();
        if (count($args) < 1) {
            throw new InvalidArgumentException(
                sprintf('Factory needs at least one argument to build')
            );
        }

        try {
            if ($args[0] instanceof ContainerInterface) {
                return static::buildRuleFromContainer(...$args);
            }
        } catch (NotFoundException $notFound) {
            // This is not the exception you're looking for
        }

        return static::buildRuleFromClass(...$args);
    }
}
