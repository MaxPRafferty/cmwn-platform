<?php

namespace Rule\Rule\Collection;

/**
 * Trait RuleCollectionAwareTrait
 */
trait RuleCollectionAwareTrait
{
    /**
     * @var RuleCollectionInterface
     */
    protected $ruleCollection;

    /**
     * Pass in a collection of rules
     *
     * This is designed to be fluent
     *
     * @param RuleCollectionInterface $collection
     */
    public function setRulesCollection(RuleCollectionInterface $collection)
    {
        $this->ruleCollection = $collection;
    }

    /**
     * Return back a collection of rules
     *
     * @return RuleCollectionInterface
     */
    public function getRulesCollection(): RuleCollectionInterface
    {
        return $this->ruleCollection;
    }
}
