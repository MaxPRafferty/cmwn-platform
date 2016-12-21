<?php

namespace Rule\Rule;

use Rule\Item\RuleItemInterface;

/**
 * Basic contract for a rule
 */
interface RuleInterface
{
    /**
     * Tests if the event satisfies a rule
     *
     * @param RuleItemInterface $item
     *
     * @return bool
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool;

    /**
     * The number of times this rule has been satisfied
     *
     * @return int
     */
    public function timesSatisfied(): int;
}
