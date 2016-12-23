<?php

namespace Rule\Rule\Basic;

use Rule\Item\RuleItemInterface;
use Rule\Rule\Collection\RuleCollection;
use Rule\Rule\Collection\RuleCollectionAwareInterface;
use Rule\Rule\Collection\RuleCollectionInterface;
use Rule\Rule\RuleInterface;

/**
 * A collection of rule(s) must all be satisfied in order to satisfy this rule
 */
class AndRule implements RuleInterface, RuleCollectionAwareInterface
{
    /**
     * @var RuleCollection
     */
    protected $rules;

    /**
     * AndSpecification constructor.
     *
     * @param \Rule\Rule\RuleInterface[] ...$rules
     */
    public function __construct(RuleInterface ...$rules)
    {
        $this->rules = new RuleCollection();
        array_walk($rules, function (RuleInterface $rule) {
            $this->rules->append($rule);
        });
    }

    /**
     * @inheritDoc
     */
    public function setRulesCollection(RuleCollectionInterface $collection)
    {
        $this->rules = $collection;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRulesCollection(): RuleCollectionInterface
    {
        return $this->rules;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        $this->rules->isSatisfiedBy($item);
        return $this->rules->timesSatisfied() === $this->rules->count();
    }

    /**
     * @inheritDoc
     */
    public function timesSatisfied(): int
    {
        return $this->rules->timesSatisfied();
    }
}
