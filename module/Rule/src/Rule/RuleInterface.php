<?php

namespace Rule;

use Rule\Item\RuleItemInterface;

/**
 * Basic contract for a rule
 */
interface RuleInterface
{
    /**
     * Tests if the event satisfies a rule
     *
     * @param RuleItemInterface $event
     *
     * @return bool
     */
    public function isSatisfiedBy(RuleItemInterface $event): bool;

    /**
     * The number of times this rule has been satisfied
     *
     * @return int
     */
    public function timesSatisfied(): int;
}
