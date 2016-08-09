<?php

namespace Application\Utils;

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
        try {
            StaticType::getLabelForType($value);

            return true;
        } catch (\InvalidArgumentException $invalidType) {
        }

        $this->error(static::INVALID_TYPE);

        return false;
    }
}
