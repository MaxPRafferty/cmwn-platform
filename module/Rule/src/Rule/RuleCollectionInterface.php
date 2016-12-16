<?php

namespace Rule;

/**
 * An Interface that defines a collection of rules
 */
interface RuleCollectionInterface extends \IteratorAggregate, RuleInterface
{
    /**
     * Allows adding rules to the collection
     *
     * @param RuleInterface $rule
     *
     * @return RuleCollectionInterface
     */
    public function append(RuleInterface $rule): RuleCollectionInterface;
}
