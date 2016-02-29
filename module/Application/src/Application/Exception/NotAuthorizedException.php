<?php

namespace Application\Exception;

/**
 * Class NotAuthorizedException
 * @package Application\Exception
 */
class NotAuthorizedException extends \Exception
{
    public function __construct($message = null, $code = null, \Exception $previous = null)
    {
        $code = 401;
        parent::__construct($message, $code, $previous);
    }
}
