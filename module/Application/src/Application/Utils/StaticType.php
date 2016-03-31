<?php

namespace Application\Utils;

/**
 * Class StaticType
 */
class StaticType
{
    protected static $types = [];

    /**
     * @param array $types
     */
    public static function setTypes(array $types)
    {
        static::$types = $types;
    }

    /**
     * @param $type
     *
     * @return mixed
     */
    public static function getLabelForType($type)
    {
        if (!static::isValid($type)) {
            throw new \InvalidArgumentException('Invalid type specified: ' . $type);
        }

        return static::$types[$type];
    }

    /**
     * @param $type
     *
     * @return mixed
     */
    public static function isValid($type)
    {
        return array_key_exists($type, static::$types);
    }
}
