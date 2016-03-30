<?php

namespace User;

class Adult extends User implements AdultInterface
{
    /**
     * @return string
     */
    public function getType()
    {
        return static::TYPE_ADULT;
    }
}
