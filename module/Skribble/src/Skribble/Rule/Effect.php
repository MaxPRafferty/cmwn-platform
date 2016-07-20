<?php

namespace Skribble\Rule;

/**
 * Class Effect
 */
class Effect extends AbstractRule
{
    /**
     * @inheritDoc
     * @todo confirm asset exists
     */
    public function isValid()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getRuleType()
    {
        return 'effect';
    }
}
