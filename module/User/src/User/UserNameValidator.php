<?php

namespace User;

use User\Service\StaticNameService;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

/**
 * Class UserNameValidator
 */
class UserNameValidator extends AbstractValidator
{
    const INVLAID = 'usernameInvalid';

    protected $messageTemplates = [
        self::INVLAID => 'Invalid username "%value%"',
    ];

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value
     *
     * @return bool
     * @throws Exception\RuntimeException If validation of $value is impossible
     */
    public function isValid($value)
    {
        if (!$value instanceof UserName) {
            list($left, $right) = explode(UserName::SEPARATOR, $value, 2);
            $value = new UserName($left, $right);
        }

        if (!StaticNameService::validateGeneratedName($value)) {
            $this->error(static::INVLAID);
            return false;
        }

        return true;
    }
}
