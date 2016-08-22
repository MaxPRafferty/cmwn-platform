<?php

namespace Application\Exception;

/**
 * Class NotFoundException
 *
 * Throw when Something is not found
 */
class NotFoundException extends \Exception
{
    /**
     * NotFoundException constructor.
     * @param null $message
     * @param null $code
     * @param \Exception|null $previous
     */
    public function __construct($message = null, $code = null, \Exception $previous = null)
    {
        $code = 404;
        $message = null === $message ? 'Not Found' : $message;
        parent::__construct($message, $code, $previous);
    }
}
