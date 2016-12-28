<?php

namespace Rule\Rule\Collection;

/**
 * States that a class must be allowed to take in a collection of rules
 */
interface RuleCollectionAwareInterface
{
    /**
     * Pass in a collection of rules
     *
     * This is designed to be fluent
     *
     * @param RuleCollectionInterface $collection
     */
    public function setRulesCollection(RuleCollectionInterface $collection);

    /**
     * Return back a collection of rules
     *
     * @return RuleCollectionInterface
     */
    public function getRulesCollection(): RuleCollectionInterface;
}
