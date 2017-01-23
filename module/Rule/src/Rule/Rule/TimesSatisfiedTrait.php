<?php

namespace Rule\Rule;

/**
 * Helper trait that rules can use to implement the timesSatisfied
 * for the RuleInterface
 */
trait TimesSatisfiedTrait
{
    /**
     * @var int
     */
    protected $timesSatisfied = 0;

    /**
     * Helps fulfill the timesSatisfied for a rule
     *
     * @return int
     */
    public function timesSatisfied(): int
    {
        return $this->timesSatisfied;
    }
}
