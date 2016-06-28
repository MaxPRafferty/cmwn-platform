<?php

namespace Skribble\Rule;

/**
 * Class RuleCollection
 */
class RuleCollection extends \ArrayObject
{
    /**
     * Ensures that we only have rule objects in this collection
     *
     * @param mixed $index
     * @param mixed $rule
     */
    public function offsetSet($index, $rule)
    {
        if (!$rule instanceof RuleInterface) {
            throw new \InvalidArgumentException('Only instances of RuleInterfaces can be set');
        }

        parent::offsetSet($index, $rule);
    }
}
