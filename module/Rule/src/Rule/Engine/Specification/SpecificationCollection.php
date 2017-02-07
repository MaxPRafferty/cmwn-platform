<?php

namespace Rule\Engine\Specification;

use Rule\Exception\RuntimeException;

/**
 * A Collection of Specifications
 */
class SpecificationCollection implements SpecificationCollectionInterface
{
    /**
     * @var \ArrayObject|RuleInterface[]
     */
    protected $specs;

    /**
     * Sets up the iterator for the rules
     */
    public function __construct()
    {
        $this->specs = new \ArrayObject();
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return $this->specs->getIterator();
    }

    /**
     * @inheritDoc
     */
    public function append(SpecificationInterface $spec): SpecificationCollectionInterface
    {
        if ($this->specs->offsetExists($spec->getId())) {
            throw new RuntimeException(sprintf(
                'A specification with the id %s already exists',
                $spec->getId()
            ));
        }

        $this->specs->offsetSet($spec->getId(), $spec);
        return $this;
    }
}
