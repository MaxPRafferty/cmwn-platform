<?php

namespace RuleTest;

use Rule\Item\RuleItemInterface;
use Rule\RuleInterface;

/**
 * Class TestSerializedRule
 */
class TestSerializedRule implements RuleInterface, \Serializable
{
    public $data;

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        // TODO: Implement serialize() method.
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $this->data = $serialized;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function timesSatisfied(): int
    {
        return 0;
    }
}
