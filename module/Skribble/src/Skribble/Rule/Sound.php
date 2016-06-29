<?php

namespace Skribble\Rule;

/**
 * Class Sound
 */
class Sound extends AbstractRule
{
    /**
     * @inheritDoc
     * @todo validate that the asset exists
     */
    public function isValid()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return 'sound';
    }
}
