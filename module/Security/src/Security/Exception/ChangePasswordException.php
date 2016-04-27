<?php

namespace Security\Exception;

use Security\ChangePasswordUser;

/**
 * Class ChangePasswordException
 *
 * Exception that is thrown when the User Needs to change their password
 */
class ChangePasswordException extends \Exception
{
    /**
     * @var ChangePasswordUser
     */
    protected $user;

    /**
     * ChangePasswordException constructor.
     * @param ChangePasswordUser $user
     * @param null $message
     * @param null $code
     * @param \Exception|null $previous
     */
    public function __construct(ChangePasswordUser $user, $message = null, $code = null, \Exception $previous = null)
    {
        $this->user = $user;
        $code       = 401;
        $message    = $message === null ? 'RESET_PASSWORD' : $message;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ChangePasswordUser
     */
    public function getUser()
    {
        return $this->user;
    }
}
