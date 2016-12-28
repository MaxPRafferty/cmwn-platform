<?php

namespace Rule\Engine\Specification;

use Interop\Container\ContainerInterface;
use Rule\Action\Collection\ActionCollection;
use Rule\Action\Collection\ActionCollectionInterface;
use Rule\Action\ActionInterface;
use Rule\Action\StaticActionFactory;
use Rule\Exception\InvalidArgumentException;
use Rule\Exception\RuntimeException;
use Rule\Provider\Collection\ProviderCollectionInterface;
use Rule\Provider\StaticProviderCollectionFactory;
use Rule\Rule\Collection\RuleCollection;
use Rule\Rule\Collection\RuleCollectionInterface;
use Rule\Rule\Service\RuleManager;
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
     * @var \Rule\Action\Collection\ActionCollectionInterface
     */
    protected $actions;

    /**
     * @var ProviderCollectionInterface
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
    public function getRules(RuleManager $services): RuleCollectionInterface
    {
        if (null !== $this->rules) {
            return $this->rules;
        }

        $this->rules = new RuleCollection();
        array_walk($this->spec['rules'], function ($ruleSpec) use (&$services) {
            $operator = $ruleSpec['operator'] ?? 'and';
            $name     = $ruleSpec['rule']['name'] ?? null;
            $options  = $ruleSpec['rule']['options'] ?? [];
            $rule     = $services->build($name, $options);
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
    public function buildProvider(ContainerInterface $services): ProviderCollectionInterface
    {
        if (null === $this->ruleItem) {
            $this->ruleItem = StaticProviderCollectionFactory::build($services, $this->spec['item_params'] ?? []);
        }

        return $this->ruleItem;
    }
}
