<?php

namespace Rule\Provider\Collection;

use Rule\Provider\ProviderInterface;

/**
 * Class ProviderIterator
 */
class ProviderIterator extends \ArrayIterator implements \Iterator
{
    /**
     * @inheritDoc
     */
    public function current()
    {
        $value = parent::current();
        return ($value instanceof ProviderInterface) ? $value->getValue() : $value;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        $value = parent::key();
        return ($value instanceof ProviderInterface) ? $value->getName() : $value;
    }
}
