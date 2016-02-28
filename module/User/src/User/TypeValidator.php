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

    protected $messageTemplates = [
        self::BIRTHDATE_REQUIRED => 'Birthdate is required when user type is child',
        self::NOT_IN_ARRAY      => 'Invalid Type'
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
        $typeOk   = parent::isValid($value);
        $birthdate = isset($context['birthdate']) ? $context['birthdate'] : null;

        if ($value !== UserInterface::TYPE_CHILD) {
            return $typeOk;
        }

        if (empty($birthdate)) {
            $this->error(static::BIRTHDATE_REQUIRED);
            return false;
        }

        return true;
    }
}
