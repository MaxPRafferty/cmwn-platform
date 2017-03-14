<?php

namespace Application\Utils\Type;

/**
 * Class StaticType
 *
 * A List of types for Organizations and Groups.  Ensures that
 * arbitrary data is not passed in forcing bugs in the FE
 *
 * @deprecated
 */
class StaticType
{
    /**
     * @var array
     */
    protected static $types = [];

    /**
     * @param array $types
     */
    public static function setTypes(array $types)
    {
        static::$types = $types;
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return static::$types;
    }

    /**
     * @param $type
     *
     * @return mixed
     */
    public static function getLabelForType($type)
    {
        if (!static::isValid($type)) {
            throw new \InvalidArgumentException('Invalid type specified');
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
