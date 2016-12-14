<?php

namespace Rule\Basic;

use Rule\RuleItemInterface;
use Rule\RuleInterface;
use Rule\TimesSatisfiedTrait;

/**
 * A collection of rule(s) must all be satisfied in order to satisfy this rule
 */
class AndSpecification implements RuleInterface
{
    use TimesSatisfiedTrait;

    /**
     * @var RuleInterface[]
     */
    protected $rules;

    /**
     * AndSpecification constructor.
     *
     * @param RuleInterface[] ...$rules
     */
    public function __construct(RuleInterface ...$rules)
    {
        $this->rules = $rules;
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

        return $this->timesSatisfied() == count($this->rules);
    }
}
