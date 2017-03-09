<?php

namespace Application\Utils;

use Zend\Validator\Exception;

/**
 * Validates the db records based on values to be inserted
 */
class CheckIfNoDbRecordExists extends AbstractDbValidator
{
    /**
     * @inheritDoc
     */
    public function isValid($value, $context = null)
    {
        if (null === $this->adapter) {
            throw new Exception\RuntimeException('No database adapter present');
        }

        $exclude = $this->getExclude();

        if (is_array($exclude) && !isset($exclude['value'])) {
            if (!isset($context[$exclude['field']])) {
                return false;
            }

            $exclude['value'] = $context[$exclude['field']];
            $this->setExclude($exclude);
        }

        $valid = true;
        $this->setValue($value);

        $result = $this->query($value);
        if ($result) {
            $valid = false;
            $this->error(self::ERROR_RECORD_FOUND);
        }

        return $valid;
    }
}
