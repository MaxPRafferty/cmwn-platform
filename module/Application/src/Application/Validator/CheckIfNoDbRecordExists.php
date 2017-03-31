<?php

namespace Application\Validator;

/**
 * Validates the inputs if the db record with given values does not exist.
 */
class CheckIfNoDbRecordExists extends AbstractDbValidator
{
    /**
     * @inheritDoc
     */
    public function isValid($value, $context = null)
    {
        $valid = true;
        $this->setValue($value);
        $this->prepareExclude($context);

        $result = $this->query($value);
        if ($result) {
            $valid = false;
            $this->error(self::ERROR_RECORD_FOUND);
        }

        return $valid;
    }
}
