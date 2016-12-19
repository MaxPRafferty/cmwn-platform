<?php

namespace RuleTest\Action;

use Rule\Action\ActionInterface;
use Rule\Item\RuleItemInterface;

/**
 * Class TestAction
 */
class TestAction implements \Serializable, ActionInterface
{
    public $constructData;

    public $serializeData;

    public function __construct(...$options)
    {
        $this->constructData = $options;
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $this->serializeData = $serialized;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RuleItemInterface $item)
    {
        // Move along Move Along
    }
}
