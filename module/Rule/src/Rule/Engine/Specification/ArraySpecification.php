<?php

namespace Rule\Engine\Specification;

use Rule\Action\Collection\ActionCollectionInterface;
use Rule\Action\Service\ActionManager;
use Rule\Action\StaticActionFactory;
use Rule\Exception\InvalidArgumentException;
use Rule\Exception\RuntimeException;
use Rule\Provider\Collection\ProviderCollectionInterface;
use Rule\Provider\Service\ProviderManager;
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
    protected $provider;

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

        // check that items are arrays
        foreach (['rules', 'actions', 'providers'] as $mustBeArray) {
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
    public function getRules(RuleManager $ruleManager): RuleCollectionInterface
    {
        if (null == $this->rules) {
            $this->rules = $ruleManager->build(
                RuleCollection::class,
                $this->spec['rules']
            );
        }

        return $this->rules;
    }

    /**
     * @inheritDoc
     */
    public function getActions(ActionManager $actionManager): ActionCollectionInterface
    {
        if (null == $this->actions) {
            $this->actions = $actionManager->build(
                ActionCollectionInterface::class,
                $this->spec['actions']
            );
        }

        return $this->actions;
    }

    /**
     * @inheritDoc
     */
    public function buildProvider(ProviderManager $providerManager): ProviderCollectionInterface
    {
        if (null === $this->provider) {
            $this->provider = $providerManager->build(
                ProviderCollectionInterface::class,
                $this->spec['providers'] ?? []
            );
        }

        return $this->provider;
    }
}
