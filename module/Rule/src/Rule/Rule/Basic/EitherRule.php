<?php

namespace Rule\Rule\Basic;

use Rule\Item\RuleItemInterface;
use Rule\Rule\Collection\RuleCollection;
use Rule\Rule\Collection\RuleCollectionAwareInterface;
use Rule\Rule\Collection\RuleCollectionInterface;
use Rule\Rule\RuleInterface;

/**
 * A Specification that is satisfied when one rule is satisfied
 */
class EitherRule implements RuleInterface, RuleCollectionAwareInterface
{
    protected $rules;

    /**
     * EitherSpecification constructor.
     *
     * @param RuleInterface[] ...$rules
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
    public function timesSatisfied(): int
    {
        return $this->rules->timesSatisfied();
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $event): bool
    {
        $this->rules->isSatisfiedBy($event);
        return $this->timesSatisfied() > 0;
    }
}
