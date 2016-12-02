<?php

namespace Application\Exception;

/**
 * Class PreConditionFailedException
 * @package Application\Exception
 */
class PreConditionFailedException extends \Exception
{
    /**
     * PreConditionFailedException constructor.
     */
    public function __construct()
    {
        parent::__construct('Precondition Failed', 412);
    }
}
