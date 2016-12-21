<?php

namespace Rule\Rule\Basic;

use Rule\Item\RuleItemInterface;
use Rule\Rule\Collection\RuleCollection;
use Rule\Rule\RuleInterface;

/**
 * A Specification that is satisfied when one rule is satisfied
 */
class EitherRule extends RuleCollection implements RuleInterface
{
    /**
     * EitherSpecification constructor.
     *
     * @param RuleInterface[] ...$rules
     */
    public function __construct(RuleInterface ...$rules)
    {
        parent::__construct();
        array_walk($rules, function (RuleInterface $rule) {
            $this->append($rule);
        });
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $event): bool
    {
        parent::isSatisfiedBy($event);
        return $this->timesSatisfied() > 0;
    }
}
