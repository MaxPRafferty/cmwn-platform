<?php

namespace Rule\Engine;

use Interop\Container\ContainerInterface;
use Rule\Action\ActionCollection;
use Rule\Action\ActionCollectionInterface;
use Rule\Action\ActionInterface;
use Rule\Action\StaticActionFactory;
use Rule\Item\RuleItemInterface;
use Rule\Provider\StaticProviderCollectionFactory;
use Rule\RuleCollection;
use Rule\RuleCollectionInterface;
use Rule\StaticRuleFactory;

/**
 * An Engine Specification that is built from an array
 */
class ArraySpecification implements EngineSpecificationInterface
{
    /**
     * @var array
     */
    protected $spec;

    /**
     * @var RuleCollectionInterface
     */
    protected $rules;

    /**
     * @var ActionCollectionInterface
     */
    protected $actions;

    /**
     * @var RuleItemInterface
     */
    protected $ruleItem;

    /**
     * Used to specify engine rules from an array
     *
     * @param array $spec
     */
    public function __construct(array $spec)
    {
        $this->spec = $spec;
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->spec['id'];
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->spec['name'];
    }

    /**
     * @inheritDoc
     */
    public function getSharedEventIdentifier(): string
    {
        return $this->spec['when']['identifier'];
    }

    /**
     * @inheritDoc
     */
    public function getEventName(): string
    {
        return $this->spec['when']['event'];
    }

    /**
     * @inheritDoc
     */
    public function getRules(ContainerInterface $services): RuleCollectionInterface
    {
        if (null !== $this->rules) {
            return $this->rules;
        }

        $this->rules = new RuleCollection();
        array_walk($this->spec['rules'], function ($ruleSpec) use (&$services) {
            $operator = $ruleSpec['operator'] ?? 'and';
            $rule = StaticRuleFactory::build($services, ...array_values($ruleSpec['rule']));
            $this->rules->append($rule, $operator);
        });

        return $this->rules;
    }

    /**
     * @inheritDoc
     */
    public function getActions(ContainerInterface $services): ActionCollectionInterface
    {
        if (null === $this->actions) {
            $this->actions = new ActionCollection();
            array_walk($this->spec['actions'], function ($actionSpec) use (&$services) {
                if ($actionSpec instanceof ActionInterface) {
                    $this->actions->append($actionSpec);
                    return;
                }

                $this->actions->append(StaticActionFactory::build($services, $actionSpec));
            });
        }

        return $this->actions;
    }

    /**
     * @inheritDoc
     */
    public function buildItem(ContainerInterface $services): RuleItemInterface
    {
        if (null === $this->ruleItem) {
            $this->ruleItem = StaticProviderCollectionFactory::build($services, $this->spec['item_params']);
        }

        return $this->ruleItem;
    }
}
