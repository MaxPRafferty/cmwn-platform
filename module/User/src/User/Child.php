<?php

namespace User;

class Child extends User
{
    /**
     * @return string
     */
    public function getType()
    {
        return static::TYPE_CHILD;
    }
}
