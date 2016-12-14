<?php

namespace Rule\Basic;

use Rule\RuleItemInterface;
use Rule\RuleInterface;
use Rule\TimesSatisfiedTrait;

/**
 * A Specification that is satisfied when one rule is satisfied
 */
class EitherSpecification implements RuleInterface
{
    use TimesSatisfiedTrait;

    /**
     * @var RuleInterface[]
     */
    protected $rules;

    /**
     * EitherSpecification constructor.
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

        return $this->timesSatisfied() > 0;
    }
}
