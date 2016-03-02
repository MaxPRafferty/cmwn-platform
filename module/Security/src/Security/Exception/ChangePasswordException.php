<?php


namespace Security\Exception;


class ChangePasswordException extends \Exception
{
    public function __construct($message = null, $code = null, \Exception $previous = null)
    {
        $code    = 403;
        $message = $message === null ? 'Reset password' : $message;
        parent::__construct($message, $code, $previous);
    }
}
