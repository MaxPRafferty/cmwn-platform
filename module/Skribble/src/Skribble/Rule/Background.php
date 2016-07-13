<?php

namespace Skribble\Rule;

/**
 * Class Background
 */
class Background extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function getRuleType()
    {
        return 'background';
    }

    /**
     * @inheritDoc
     * @todo Validate that the asset exists
     */
    public function isValid()
    {
        return true;
    }
}
