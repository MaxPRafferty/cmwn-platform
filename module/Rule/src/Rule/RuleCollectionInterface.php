<?php

namespace Rule;

/**
 * An Interface that defines a collection of rules
 */
interface RuleCollectionInterface extends \IteratorAggregate, RuleInterface
{
    const OPERATOR_AND = 'and';
    const OPERATOR_OR  = 'or';
    const OPERATOR_NOT = 'not';

    /**
     * Adds rules to the collection
     *
     * This is designed to be a fluent object
     *
     * @param RuleInterface $rule
     * @param string $operator
     * @param string|null $orGroup
     *
     * @return RuleCollectionInterface
     */
    public function append(
        RuleInterface $rule,
        string $operator = self::OPERATOR_AND,
        string $orGroup = null
    ): RuleCollectionInterface;
}
