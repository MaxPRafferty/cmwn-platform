<?php

namespace Application\Validator;

/**
 * Validates the inputs if the db record with given values exist.
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class CheckIfDbRecordExists extends AbstractDbValidator
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
        if (!$result) {
            $valid = false;
            $this->error(self::ERROR_NO_RECORD_FOUND);
        }

        return $valid;
    }
}
