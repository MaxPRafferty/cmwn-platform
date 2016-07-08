<?php

namespace Skribble\Rule;

/**
 * Class Item
 */
class Item extends AbstractRule implements StateRuleInterface
{
    use StateRuleTrait;

    /**
     * @inheritDoc
     */
    public function isValid()
    {
        return $this->isStateValid();
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return 'item';
    }
}
