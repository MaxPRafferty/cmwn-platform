<?php

namespace Friend;

use Exception;

/**
 * Class NotFriendsException
 *
 * Use for when two users are not friends
 *
 * @link https://www.flickr.com/photos/gladiolabean/6291666746
 */
class NotFriendsException extends \RuntimeException
{
    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link http://php.net/manual/en/exception.construct.php
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param Exception $previous [optional] The previous exception used for the exception chaining. Since 5.3.0
     * @since 5.1.0
     */
    public function __construct($message, $code, Exception $previous)
    {
        parent::__construct('Not Friends', 421, $previous);
    }
}
