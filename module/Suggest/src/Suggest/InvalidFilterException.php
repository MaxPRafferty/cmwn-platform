<?php

namespace Suggest;

use Exception;

/**
 * Class InvalidFilterException
 */
class InvalidFilterException extends InvalidArgumentException
{
    /**
     * InvalidFilterException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($message = 'Invalid Filter Provided', $code = 500, Exception $previous = null)
    {
        parent::__construct('Invalid Filter Provided', $code, $previous);
    }
}
