<?php

namespace Application\Exception;

/**
 * Exception DuplicateEntryException
 *
 * A duplicate entry was attempted to be saved
 */
class DuplicateEntryException extends \Exception
{
    /**
     * DuplicateEntryException constructor.
     *
     * @param null $message
     * @param null $code
     * @param \Exception|null $previous
     */
    public function __construct($message = null, $code = null, \Exception $previous = null)
    {
        $code = 409;
        parent::__construct($message, $code, $previous);
    }
}
