<?php

namespace Rule\Engine\Specification;

use Interop\Container\ContainerInterface;
use Rule\Action\ActionCollection;
use Rule\Action\ActionCollectionInterface;
use Rule\Action\ActionInterface;
use Rule\Action\StaticActionFactory;
use Rule\Exception\InvalidArgumentException;
use Rule\Exception\RuntimeException;
use Rule\Item\RuleItemInterface;
use Rule\Provider\StaticProviderCollectionFactory;
use Rule\Rule\Collection\RuleCollection;
use Rule\Rule\Collection\RuleCollectionInterface;
use Rule\Rule\StaticRuleFactory;

/**
 * An Engine Specification that is built from an array
 */
class ArraySpecification implements SpecificationInterface
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
        // Check that we have all the required key
        foreach (['id', 'name', 'when', 'rules', 'actions'] as $required) {
            if (!isset($spec[$required]) || empty($spec[$required])) {
                throw new RuntimeException(sprintf(
                    'Missing required key "%s" for "%s"',
                    $required,
                    static::class
                ));
            }
        }

        foreach (['rules', 'actions', 'item_params'] as $mustBeArray) {
            if (isset($spec[$mustBeArray]) && !is_array($spec[$mustBeArray])) {
                throw new InvalidArgumentException(sprintf(
                    'Key "%s" myst be an array for "%s"',
                    $mustBeArray,
                    static::class
                ));
            }
        }

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
    public function getEventName(): string
    {
        return $this->spec['when'];
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
            $rule     = StaticRuleFactory::build($services, ...array_values($ruleSpec['rule']));
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
            $this->ruleItem = StaticProviderCollectionFactory::build($services, $this->spec['item_params'] ?? []);
        }

        return $this->ruleItem;
    }
}
