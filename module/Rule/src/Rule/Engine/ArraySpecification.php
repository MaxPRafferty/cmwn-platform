<?php

namespace Rule\Engine;

use Interop\Container\ContainerInterface;
use Rule\Action\ActionCollectionInterface;
use Rule\Item\RuleItemInterface;
use Rule\RuleCollection;
use Rule\RuleCollectionInterface;
use Rule\StaticRuleFactory;

/**
 * Class ArraySpecification
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
        if (null === $this->rules) {
            $this->rules = new RuleCollection();
            array_walk($this->spec['rules'], function ($ruleSpec) use (&$services) {
                $this->rules->append(StaticRuleFactory::buildRule($services, ...array_values($ruleSpec)));
            });
        }

        return $this->rules;
    }

    /**
     * @inheritDoc
     */
    public function getActions(ContainerInterface $services): ActionCollectionInterface
    {
        // TODO: Implement getActions() method.
    }

    /**
     * @inheritDoc
     */
    public function buildItem(ContainerInterface $services): RuleItemInterface
    {
        // TODO: Implement buildItem() method.
    }

}
