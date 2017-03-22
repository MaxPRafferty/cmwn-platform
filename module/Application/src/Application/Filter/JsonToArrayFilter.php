<?php

namespace Application\Filter;

use Zend\Filter\AbstractFilter;
use Zend\Json\Json;

/**
 * Ensures that passed in data is an array
 *
 * It will json decode a string
 */
class JsonToArrayFilter extends AbstractFilter
{
    /**
     * Json Decode a string or return an array
     *
     * @param mixed $value
     *
     * @return array
     */
    public function filter($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        $return = [];
        if (is_string($value)) {
            try {
                $return = Json::decode($value, Json::TYPE_ARRAY);
            } catch (\Exception $jsonException) {
                $return = [];
            }
        }

        return $return;
    }
}