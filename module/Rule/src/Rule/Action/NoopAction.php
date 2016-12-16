<?php

namespace Rule\Action;

use Rule\Item\RuleItemInterface;

/**
 * An Action that does nothing
 */
class NoopAction implements ActionInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(RuleItemInterface $item)
    {
        // I'm lazy
    }
}
