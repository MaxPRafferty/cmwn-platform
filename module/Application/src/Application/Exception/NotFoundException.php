<?php

namespace Application\Exception;

class NotFoundException extends \Exception
{
    public function __construct($message = null, $code = null, \Exception $previous = null)
    {
        $code = 404;
        parent::__construct($message, $code, $previous);
    }
}
