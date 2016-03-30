<?php

namespace User;

use Zend\Validator\InArray;
use Zend\Validator\ValidatorInterface;

/**
 * Requires the birthdate field when type is child
 *
 * @package User
 */
class TypeValidator extends InArray implements ValidatorInterface
{
    const BIRTHDATE_REQUIRED = 'birthdateRequired';
    const USERNAME_REQUIRED  = 'usernameRequired';

    protected $messageTemplates = [
        self::BIRTHDATE_REQUIRED => 'Birthdate is required when user type is child',
        self::USERNAME_REQUIRED  => 'Username is required when user type is adult',
        self::NOT_IN_ARRAY       => 'Invalid Type'
    ];

    protected $haystack = [
        UserInterface::TYPE_CHILD,
        UserInterface::TYPE_ADULT
    ];

    /**
     * Requires the birthdate when the type of user is CHILD
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function isValid($value, $context = [])
    {
        $typeOk    = parent::isValid($value);

        if (!$typeOk) {
            return $typeOk;
        }

        switch ($value) {
            case UserInterface::TYPE_CHILD:
                return $this->validateChild($context);

            case UserInterface::TYPE_ADULT:
                return $this->validateAdult($context);

            default:
                // Log invalid type here?
        }

        return false;
    }

    protected function validateAdult($context)
    {
        $username = isset($context['username']) ? $context['username'] : null;

        if (empty($username)) {
            $this->error(static::USERNAME_REQUIRED);
            return false;
        }

        return true;
    }

    protected function validateChild($context)
    {
        $birthdate = isset($context['birthdate']) ? $context['birthdate'] : null;

        if (empty($birthdate)) {
            $this->error(static::BIRTHDATE_REQUIRED);
            return false;
        }

        return true;
    }
}
