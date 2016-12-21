<?php

namespace Rule\Rule\Collection;

use Interop\Container\ContainerInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\StaticRuleFactory;

/**
 * Class StaticRuleCollectionFactory
 */
class StaticRuleCollectionFactory
{
    /**
     * Builds a collection of rules from an array
     *
     * Each item should have the following keys:
     *
     * <code>
     * [
     *      'rule' => [
     *          'name'    => Class or Service name of rule,
     *          'options' => [ Options for rule class or service ],
     *      ],
     *      'operator' => and | or | not,
     *      'or_group' => Name of the group when building a or rule
     * ]
     * </code>
     *
     * @param ContainerInterface $services
     * @param $ruleSpec
     *
     * @return RuleCollection
     */
    public static function build(ContainerInterface $services, $ruleSpec)
    {
        $rules = new RuleCollection();
        array_walk($ruleSpec, function ($ruleSpec) use (&$services, $rules) {
            $operator = RuleCollection::OPERATOR_AND;
            $group    = null;
            $rule     = $ruleSpec;
            if (!$ruleSpec instanceof RuleInterface) {
                $operator = $ruleSpec['operator'] ?? RuleCollection::OPERATOR_AND;
                $group    = $ruleSpec['or_group'] ?? null;
                $rule     = StaticRuleFactory::build($services, ...array_values($ruleSpec['rule']));
            }

            $rules->append($rule, $operator, $group);
        });

        return $rules;
    }
}
