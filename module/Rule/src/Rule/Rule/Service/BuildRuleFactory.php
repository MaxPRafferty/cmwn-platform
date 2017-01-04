<?php

namespace Rule\Rule\Service;

use Interop\Container\ContainerInterface;
use Rule\Rule\RuleInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * A Factory that can be used to build an rule
 *
 * Intended for use with the RuleManager::build(), however can be used much like the ZF3 invokable factory
 *
 * Every key in the $options will be check for a corresponding name in the container.  If not then the natural
 * value will be passed into the class on construction
 */
class BuildRuleFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options   = is_array($options) ? $options : [];
        $ruleClass = $options['rule_class'] ?? $requestedName;
        unset($options['rule_class']);

        if (!in_array(RuleInterface::class, class_implements($ruleClass))) {
            throw new ServiceNotCreatedException(
                sprintf(
                    'Cannot create "%s" using "%s" since "%s" is not an rule',
                    $ruleClass,
                    self::class,
                    $ruleClass
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
            $options
        );

        return new $ruleClass(...$arguments);
    }
}
