<?php

namespace Application\Exception;

class DuplicateEntryException extends \Exception
{
    public function __construct($message = null, $code = null, \Exception $previous = null)
    {
        $code = 409;
        parent::__construct($message, $code, $previous);
    }
}
