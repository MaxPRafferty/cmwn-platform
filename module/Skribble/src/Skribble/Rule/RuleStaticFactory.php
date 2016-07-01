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

        $className = __NAMESPACE__ . '\\' . ucfirst(mb_strtolower($options['type']));
        if (!class_exists($className)) {
            throw new \RuntimeException(
                sprintf('Cannot create rule of type "%s": does not exist', $options['type'])
            );
        }

        $rule = new $className;
        if (!$rule instanceof RuleCompositeInterface) {
            throw new \RuntimeException(
                sprintf('Cannot create rule of type "%s": is not a rule', $options['type'])
            );
        }

        $rule->exchangeArray($options);
        return $rule;
    }
}
