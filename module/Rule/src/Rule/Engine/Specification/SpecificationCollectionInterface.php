<?php

namespace Rule\Engine\Specification;

/**
 * An Interface that defines a collection of specifications
 */
interface SpecificationCollectionInterface extends \IteratorAggregate
{
    /**
     * Adds a specification to the collection
     *
     * This is designed to be fluent
     *
     * @param SpecificationInterface $spec
     *
     * @return SpecificationCollectionInterface
     */
    public function append(SpecificationInterface $spec): SpecificationCollectionInterface;
}
