<?php

namespace Security;

use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Exception\ChangePasswordException;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\Exception;
use Zend\Validator\Regex;
use Zend\Validator\ValidatorInterface;

/**
 * Class PasswordValidator
 *
 * Validates a password that is passed in
 */
class PasswordValidator extends Regex implements ValidatorInterface, AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;

    const NEW_PASSWORD = 'newPassword';
    const CASE_CHANGE = 'caseChange';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID      => "Password must be at least 8 characters with one of them being a number",
        self::NOT_MATCH    => "Password must be at least 8 characters with one of them being a number",
        self::ERROROUS     => "Password must be at least 8 characters with one of them being a number",
        self::NEW_PASSWORD => 'You must set a new password',
        self::CASE_CHANGE  => "You must create a new password and it must be different than your access code.",
    ];

    /**
     * PasswordValidator constructor.
     */
    public function __construct()
    {
        parent::__construct($this->getPattern());
    }

    /**
     * @param mixed|string $value
     * @return bool
     */
    public function isValid($value)
    {
        if (!parent::isValid($value)) {
            return false;
        }

        try {
            $loggedIn = $this->getAuthenticationService()->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $loggedIn = $changePassword->getUser();
        }

        if (!$loggedIn instanceof SecurityUser) {
            return true;
        }

        if ((null !== $loggedIn->getCode())) {
            if ($loggedIn->getCode() === $value) {
                $this->error(static::NEW_PASSWORD);
                return false;
            } elseif (strcasecmp($loggedIn->getCode(), $value) == 0) {
                $this->error(static::CASE_CHANGE);
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the pattern option
     *
     * @return string
     */
    public function getPattern()
    {
        return '/^([a-zA-Z])[a-zA-Z0-9]{7,}$/';
    }
}
