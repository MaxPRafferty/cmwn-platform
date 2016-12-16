<?php

namespace Rule\Basic;

use Rule\Item\RuleItemInterface;
use Rule\RuleInterface;

/**
 * A Rule that is never happy
 */
class NeverSatisfiedRule implements RuleInterface
{
    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $event): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function timesSatisfied(): int
    {
        return 0;
    }

}
