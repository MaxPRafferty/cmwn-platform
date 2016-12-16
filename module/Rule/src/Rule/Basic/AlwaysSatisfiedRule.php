<?php

namespace Rule\Basic;

use Rule\Item\RuleItemInterface;
use Rule\RuleInterface;

/**
 * A Rule that is always happy
 */
class AlwaysSatisfiedRule implements RuleInterface
{
    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $event): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function timesSatisfied(): int
    {
        return 1;
    }
}
