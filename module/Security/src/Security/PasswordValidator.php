<?php

namespace Security;

use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Exception\ChangePasswordException;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Regex;

/**
 * Global Password Validator
 *
 * Checks password strength
 */
class PasswordValidator extends AbstractValidator
{
    use AuthenticationServiceAwareTrait;

    const NEW_PASSWORD = 'newPassword';
    const CASE_CHANGE  = 'caseChange';
    const TOO_SHORT    = 'toShort';
    const NO_LETTER    = 'noLetter';
    const NO_NUMBER    = 'noNumber';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::TOO_SHORT    => 'Password must be at least 8 characters',
        self::NO_NUMBER    => 'You must have at least one number',
        self::NO_LETTER    => 'You must have at least one letter',
        self::NEW_PASSWORD => 'You must set a new password',
        self::CASE_CHANGE  => 'You must create a new password and it must be different than your access code.',
    ];

    /**
     * PasswordValidator constructor.
     *
     * @param AuthenticationServiceInterface $authService
     */
    public function __construct(AuthenticationServiceInterface $authService)
    {
        $this->setAuthenticationService($authService);
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function isValid($value)
    {
        return $this->checkStrength($value) && $this->checkUser($value);
    }

    /**
     * Checks the strength
     *
     * @param string $value
     *
     * @return bool
     */
    protected function checkStrength(string $value): bool
    {
        if (mb_strlen($value) < 8) {
            $this->error(static::TOO_SHORT);

            return false;
        }

        // Check for at least one number and one upper
        preg_match_all('/(?\'digit\'[[:digit:]])|(?\'letter\'[[:alpha:]])/', $value, $matches, PREG_PATTERN_ORDER);

        $letter = trim(implode("", $matches['letter'] ?? []));
        $digit  = trim(implode("", $matches['digit'] ?? []));

        if (empty($letter)) {
            $this->error(static::NO_LETTER);

            return false;
        }

        if (empty($digit)) {
            $this->error(static::NO_NUMBER);

            return false;
        }

        return true;
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    protected function checkUser(string $password): bool
    {
        try {
            $loggedIn = $this->getAuthenticationService()->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $loggedIn = $changePassword->getUser();
        }

        if (!$loggedIn instanceof SecurityUser) {
            return true;
        }

        if ((null === $loggedIn->getCode())) {
            return true;
        }

        if ($loggedIn->getCode() === $password) {
            $this->error(static::NEW_PASSWORD);

            return false;
        }

        if (strcasecmp($loggedIn->getCode(), $password) === 0) {
            $this->error(static::CASE_CHANGE);

            return false;
        }

        return true;
    }
}
