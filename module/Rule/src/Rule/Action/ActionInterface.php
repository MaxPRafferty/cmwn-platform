<?php

namespace Rule\Action;

use Rule\Item\RuleItemInterface;

/**
 * An action that happens when a rule or set of rules are satisfied
 */
interface ActionInterface
{
    /**
     * Executes the action for the item
     *
     * @param RuleItemInterface $item
     */
    public function __invoke(RuleItemInterface $item);
}
