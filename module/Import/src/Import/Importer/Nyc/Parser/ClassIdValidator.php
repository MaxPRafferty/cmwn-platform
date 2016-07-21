<?php

namespace Import\Importer\Nyc\Parser;

use Zend\Validator\Regex;

/**
 * Class ClassValidator
 */
class ClassIdValidator extends Regex
{
    /**
     * ClassIdValidator constructor.
     * @param string|\Traversable $pattern
     */
    public function __construct($pattern = null)
    {
        parent::__construct('/^\d{2}[A-Z]\d{3}-\d{3}$/');
    }

    /**
     * Validates a valid class ID
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        if (strpos($value, '8') === 7) {
            $this->pattern = '/^\d{2}[A-Z]\d{3}-8\d{3}$/';
        }

        return parent::isValid($value);
    }
}
