<?php

namespace User;

/**
 * Class Adult
 *
 * An Adult is a class of user that is considered older than 13
 */
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
