<?php

namespace Rule;

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
    public function execute(RuleItemInterface $item): void;
}
