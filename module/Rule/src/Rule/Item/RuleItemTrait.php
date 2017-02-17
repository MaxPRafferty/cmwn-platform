<?php

namespace Rule\Item;

use Rule\Engine\Specification\SpecificationInterface;

/**
 * This trait will help satisfy RuleItemInterface::setSpecification and getSpecification
 */
trait RuleItemTrait
{
    /**
     * @var SpecificationInterface
     */
    protected $spec;

    /**
     * @inheritDoc
     */
    public function setSpecification(SpecificationInterface $specification): RuleItemInterface
    {
        $this->spec = $specification;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSpecification(): SpecificationInterface
    {
        return $this->spec;
    }
}
