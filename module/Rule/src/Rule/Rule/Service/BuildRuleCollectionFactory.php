<?php

namespace Rule\Rule\Service;

use Interop\Container\ContainerInterface;
use Rule\Rule\Collection\RuleCollection;
use Rule\Rule\Collection\RuleCollectionInterface;
use Rule\Rule\RuleInterface;
use Rule\Utils\AbstractCollectionBuilder;
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
 *          'name'    => 'Some/Rule/To/Build',
 *          'options' => ['foo', 'bar'],
 *          'operator' ("and" | "or" | "not") => the operator to use when adding the collection
 *          'or_group' => when operation is "or" this will group together the rules
 *      ]
 * ]
 *
 *
 * @see BuildDependantRuleFactory
 * @see RuleCollection
 */
class BuildRuleCollectionFactory extends AbstractCollectionBuilder implements FactoryInterface
{
    /**
     * @var string
     */
    protected $collectionClassKey = 'rule_collection_class';

    /**
     * @var string
     */
    protected $collectionItemsKey = 'rules';

    /**
     * @var string
     */
    protected $collectionInstanceOf = RuleCollectionInterface::class;

    /**
     * @var string
     */
    protected $itemInstanceOf = RuleInterface::class;

    /**
     * @var string
     */
    protected $pluginManagerName = RuleManager::class;

    /**
     * @param ContainerInterface $container
     * @param array $spec
     * @param RuleCollectionInterface $collection
     */
    protected function appendArraySpec(ContainerInterface $container, array $spec, $collection)
    {
        $collection->append(
            $this->getPluginManager($container)->build(
                $spec['name'] ?? null,
                $spec['options'] ?? []
            ),
            $spec['operator'] ?? RuleCollectionInterface::OPERATOR_AND,
            $spec['or_group'] ?? ""
        );
    }
}
