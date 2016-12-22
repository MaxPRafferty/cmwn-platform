<?php

namespace Rule\Rule\Basic;

use Rule\Rule\Collection\RuleCollection;
use Rule\Rule\RuleInterface;

/**
 * A collection of rule(s) must all be satisfied in order to satisfy this rule
 */
class AndRule extends RuleCollection implements RuleInterface
{
    /**
     * AndSpecification constructor.
     *
     * @param \Rule\Rule\RuleInterface[] ...$rules
     */
    public function __construct(RuleInterface ...$rules)
    {
        parent::__construct();
        array_walk($rules, function (RuleInterface $rule) {
            $this->append($rule);
        });
    }
}
