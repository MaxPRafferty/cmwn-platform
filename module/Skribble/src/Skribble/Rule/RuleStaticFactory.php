<?php

namespace Skribble\Rule;

/**
 * Class RuleStaticFactory
 */
class RuleStaticFactory
{
    /**
     * Creates a rule from an array of options
     *
     * @param array $options
     * @return RuleCompositeInterface
     */
    public static function createRuleFromArray(array $options)
    {
        if (!isset($options['type'])) {
            throw new \RuntimeException('Cannot create rule: missing type');
        }

        $className = __NAMESPACE__ . '\\' . $options['type'];
        if (!class_exists($className)) {
            throw new \RuntimeException(
                sprintf('Cannot create rule of class %s: does not exist', $className)
            );
        }

        $rule = new $className;
        if (!$rule instanceof RuleCompositeInterface) {
            throw new \RuntimeException(
                sprintf('Cannot create rule of class %s: is not rule', $className)
            );
        }

        $rule->exchangeArray($options);
        return $rule;
    }
}
