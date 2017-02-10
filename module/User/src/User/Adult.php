<?php

namespace User;

/**
 * An Adult user
 */
class Adult extends User implements AdultInterface
{
    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return static::TYPE_ADULT;
    }
}
