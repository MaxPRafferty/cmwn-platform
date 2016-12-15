<?php

namespace Suggest;

use Exception;

/**
 * Class InvalidFilterException
 */
class InvalidSuggestionException extends InvalidArgumentException
{
    /**
     * InvalidFilterException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($message = 'Invalid Suggestion Provided', $code = 500, Exception $previous = null)
    {
        parent::__construct('Invalid Suggestion Provided', $code, $previous);
    }
}
