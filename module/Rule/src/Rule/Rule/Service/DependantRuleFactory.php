<?php

namespace Rule\Rule\Service;

use Interop\Container\ContainerInterface;
use Rule\Rule\Collection\RuleCollectionAwareInterface;
use Rule\Rule\Collection\RuleCollectionInterface;
use Rule\Rule\RuleInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * A Factory that will make a rule that has rule dependencies
 * Config options:
 *
 * 'rule_class'                      // Defines the class to use when building the class
 * 'rules' => [                      // The specification for building the rules
 *      new AlwaysSatisfiedRule(),   // Appends a built rule
 *      AlwaysSatisfiedRule::class,  // Gets a rule from the Manager
 *      [                            // Builds a rule with options
 *          'rule' => [
 *              'name' => The name of the rule to build or load
 *              'options' => Options the rule needs to build
 *          ],
 *          'operator' ("and" | "or" | "not") => the operator to use when adding the collection
 *          'or_group' => when operation is "or" this will group together the rules
 *      ]
 * ]
 *
 * Under the hood, this builds a rules collection and checks if the rule is RuleCollectionAware.  If not
 * all the rules from the collection are splatted into the constructor of the rule.
 *
 * @see DependantRuleFactory
 * @see RuleCollection
 */
class DependantRuleFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = is_array($options) ? $options : [];
        if (empty($options)) {
            return new $requestedName();
        }

        /** @var RuleManager $ruleManager */
        $ruleClass = $options['rule_class'] ?? $requestedName;
        unset($options['rule_class']);

        $ruleManager = $container->get(RuleManager::class);
        $ruleSpecs   = $options ?? [];

        // bail early if we have no specs
        if (empty($ruleSpecs)) {
            return new $ruleClass();
        }

        $ruleCollection = $ruleManager->build(
            RuleCollectionInterface::class,
            $ruleSpecs
        );

        if (!in_array(RuleCollectionAwareInterface::class, class_implements($ruleClass))) {
            return new $ruleClass(...$ruleCollection->toArray());
        }

        /** @var RuleInterface|RuleCollectionAwareInterface $rule */
        $rule = new $ruleClass();
        $rule->setRulesCollection($ruleCollection);

        return $rule;
    }
}
