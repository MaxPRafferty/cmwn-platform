<?php

namespace Suggest;

use Exception;

/**
 * Class NotFoundException
 * @package Suggest
 */
class NotFoundException extends \Exception
{
    /**
     * NotFoundException constructor.
     * @param null $message
     * @param null $code
     * @param Exception|null $previous
     */
    public function __construct($message = null, $code = null, Exception $previous = null)
    {
        parent::__construct('Suggestion Not Found', 404, $previous);
    }
}
