<?php

namespace User;

/**
 * Class StaticUserFactory
 *
 * Creates a user from an array.  Helps out with hydration mainly
 *
 * @deprecated Use the UserHydrator
 */
class StaticUserFactory
{
    /**
     * @var UserHydrator
     */
    protected static $hydrator;

    /**
     * Creates a user based on the type
     *
     * Helpful when data is coming back from the DB
     *
     * @param array|\ArrayObject $data
     * @param null $type
     *
     * @return UserInterface
     * @deprecated
     */
    public static function createUser($data, $type = null)
    {
        if (static::$hydrator == null) {
            static::$hydrator = new UserHydrator();
        }

        $data['type'] = !isset($data['type']) ? null : $data['type'];
        $data['type'] = null !== $type ? $type : $data['type'];
        return static::$hydrator->hydrate($data, null);
    }
}
