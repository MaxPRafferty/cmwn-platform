<?php

namespace Rule;

use Rule\Item\RuleItemInterface;

/**
 * A Collection of rules
 *
 * This can also be Used as the AndRule
 */
class RuleCollection implements RuleCollectionInterface
{
    use TimesSatisfiedTrait;

    /**
     * @var \ArrayIterator|RuleInterface[]
     */
    protected $rules;

    /**
     * Sets up the iterator for the rules
     */
    public function __construct()
    {
        $this->rules = new \ArrayIterator([]);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return $this->rules;
    }

    /**
     * @inheritDoc
     */
    public function append(RuleInterface $rule): RuleCollectionInterface
    {
        $this->rules->append($rule);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $event): bool
    {
        foreach ($this->rules as $rule) {
            if ($rule->isSatisfiedBy($event)) {
                $this->timesSatisfied++;
            }
        }

        return $this->timesSatisfied === $this->rules->count();
    }
}
