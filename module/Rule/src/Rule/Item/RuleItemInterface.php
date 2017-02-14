<?php

namespace Rule\Item;

use Rule\Engine\Specification\SpecificationInterface;
use Rule\Provider\ProviderInterface;

/**
 * A Rule item is an expanded event that is use to satisfy rules
 */
interface RuleItemInterface
{
    /**
     * Gets an item parameter
     *
     * @param string $param
     * @param null $default default value to return if $param is not set
     *
     * @return mixed
     */
    public function getParam(string $param, $default = null);

    /**
     * Appends a new provider
     *
     * This will allow rules and actions to provide data
     *
     * @param ProviderInterface $provider
     *
     * @return RuleItemInterface
     */
    public function append(ProviderInterface $provider): RuleItemInterface;

    /**
     * Sets the specification that generated this item
     *
     * @param SpecificationInterface $specification
     *
     * @return RuleItemInterface
     */
    public function setSpecification(SpecificationInterface $specification): RuleItemInterface;

    /**
     * Gets the specification that generated this item
     *
     * @return SpecificationInterface
     */
    public function getSpecification(): SpecificationInterface;
}
