<?php

namespace Rule\Basic;

use Rule\Item\RuleItemInterface;
use Rule\RuleInterface;
use Rule\TimesSatisfiedTrait;

/**
 * This rule will be satisfied when another rule is not satisfied
 */
class NotRule implements RuleInterface
{
    use TimesSatisfiedTrait;

    /**
     * @var RuleInterface
     */
    protected $rule;

    /**
     * NotSpecification constructor.
     *
     * @param RuleInterface $rule
     */
    public function __construct(RuleInterface $rule)
    {
        $this->rule = $rule;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $event): bool
    {
        if (!$this->rule->isSatisfiedBy($event)) {
            $this->timesSatisfied++;
        }

        return $this->timesSatisfied > 0;
    }
}
