<?php

namespace Application\Utils;

use Zend\Filter\StaticFilter;

/**
 * Class PropertiesTrait
 *
 * A Trait to help with some magic methods to access some properties of class
 */
trait PropertiesTrait
{
    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $method = 'set' . ucfirst(StaticFilter::execute($name, 'Word\UnderscoreToCamelCase'));
        if (method_exists($this, $method)) {
            $this->{$method}($value);
        }
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst(StaticFilter::execute($name, 'Word\UnderscoreToCamelCase'));
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        return null;
    }
}
