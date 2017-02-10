<?php

namespace Application\Utils\Type;

use Zend\Validator\AbstractValidator;

/**
 * Class TypeValidator
 */
class TypeValidator extends AbstractValidator
{
    const INVALID_TYPE = 'invalidType';

    protected $messages = [

    ];

    /**
     * @inheritDoc
     */
    public function isValid($value)
    {
        $this->setValue($value);
        if (defined(TypeInterface::class . '::TYPE_' . strtoupper($value))) {
            return true;
        }

        $this->error(static::INVALID_TYPE);

        return false;
    }
}
