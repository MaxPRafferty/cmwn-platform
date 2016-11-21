<?php

namespace Suggest;

use Exception;

/**
 * Class InvalidFilterException
 */
class InvalidRuleException extends InvalidArgumentException
{
    /**
     * InvalidFilterException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($message = 'Invalid Rule Provided', $code = 500, Exception $previous = null)
    {
        parent::__construct('Invalid Rule Provided', $code, $previous);
    }
}
