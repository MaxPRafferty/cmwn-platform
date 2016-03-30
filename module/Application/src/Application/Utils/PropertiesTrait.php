<?php

namespace Application\Utils;

use Zend\Filter\StaticFilter;

trait PropertiesTrait
{
    public function __set($name, $value)
    {
        $method = 'set' . ucfirst(StaticFilter::execute($name, 'Word\UnderscoreToCamelCase'));
        if (method_exists($this, $method)) {
            $this->{$method}($value);
        }
    }

    public function __get($name)
    {
        $method = 'get' . ucfirst(StaticFilter::execute($name, 'Word\UnderscoreToCamelCase'));
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        return null;
    }
}
