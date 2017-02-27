<?php

namespace Application\Utils\Meta;

use Zend\Filter\AbstractFilter;
use Zend\Json\Json;

/**
 * Class MetaFilter
 *
 * @package Application\Utils
 */
class MetaFilter extends AbstractFilter
{
    /**
     * Ensures that meta data is an array
     *
     * @param mixed $value
     * @return array|mixed
     */
    public function filter($value)
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
