<?php

namespace User;

class Adult extends User
{
    /**
     * @return string
     */
    public function getType()
    {
        return static::TYPE_ADULT;
    }
}
