<?php

namespace Rule\Rule\Service;

use Interop\Container\ContainerInterface;
use Rule\Rule\Collection\RuleCollection;
use Rule\Rule\Collection\RuleCollectionInterface;
use Rule\Rule\RuleInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * A Factory that will build a collection of rules
 *
 * Config options:
 *
 * // Define an optional rule collection
 * 'rule_collection_class' => 'Some\Rule\Collection',
 *
 * // Defines the dependencies for the rule
 * 'rules' => [
 *      // This will append the rule
 *      new AlwaysSatisfiedRule(),
 *
 *      // This will GET the rule from the manager
 *      'Manchuck/Rule',
 *      AlwaysSatisfiedRule::class,
 *
 *     // This will build a rule with options
 *     [
 *          'rule' => [
 *              'name'    => 'Some/Rule/To/Build',
 *              'options' => ['foo', 'bar'],
 *          ],
 *          'operator' ("and" | "or" | "not") => the operator to use when adding the collection
 *          'or_group' => when operation is "or" this will group together the rules
 *      ]
 * ]
 *
 *
 * @see DependantRuleFactory
 * @see RuleCollection
 */
class RuleCollectionFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = is_array($options) ? $options : [];

        // allow empty collections to be build
        if (empty($options)) {
            return new $requestedName();
        }

        /** @var RuleManager $ruleManager */
        $collectionClass = $options['rule_collection_class'] ?? $requestedName;
        $ruleCollection  = new $collectionClass();

        if (!$ruleCollection instanceof RuleCollectionInterface) {
            throw new ServiceNotCreatedException(
                sprintf('%s is not a valid rule collection', $collectionClass)
            );
        }

        unset($options['rule_collection_class']);
        $ruleManager     = $container->get(RuleManager::class);
        $collectionRules = $options['rules'] ?? $options;

        array_walk($collectionRules, function ($collectionSpec) use (&$ruleCollection, &$ruleManager) {
            $ruleSpec = is_array($collectionSpec) && isset($collectionSpec['rule'])
                ? $collectionSpec['rule']
                : $collectionSpec;

            switch (true) {
                // Get the rule from the manager
                case is_string($ruleSpec):
                    $ruleSpec = $ruleManager->get($ruleSpec);
                // we want to fall through

                // Append a built rule
                case ($ruleSpec instanceof RuleInterface):
                    $ruleCollection->append($ruleSpec);
                    break;

                // Build the rule
                case is_array($ruleSpec):
                    $ruleName    = $ruleSpec['name'] ?? null;
                    $ruleOptions = $ruleSpec['options'] ?? [];
                    $rule        = $ruleManager->build($ruleName, $ruleOptions);

                    $ruleCollection->append(
                        $rule,
                        $collectionSpec['operator'] ?? RuleCollectionInterface::OPERATOR_AND,
                        $collectionSpec['or_group'] ?? ""
                    );
                    break;

                default:
                    throw new ServiceNotCreatedException('Invalid Rule spec type');
            }
        });

        return $ruleCollection;
    }
}
