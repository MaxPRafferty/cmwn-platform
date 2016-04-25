<?php

namespace User;

/**
 * Class StaticUserFactory
 *
 * Creates a user from an array.  Helps out with hydration mainly
 */
class StaticUserFactory
{
    /**
     * Creates a user based on the type
     *
     * Helpful when data is coming back from the DB
     *
     * @param array|\ArrayObject $data
     * @param null $type
     * @return Adult|Child
     */
    public static function createUser($data, $type = null)
    {
        if (!is_array($data) && !$data instanceof \ArrayObject) {
            throw new \InvalidArgumentException('Data must be an array or ArrayObject');
        }

        if ($data instanceof \ArrayObject) {
            $data = $data->getArrayCopy();
        }

        if ($type === null) {
            $type = isset($data['type']) ? $data['type'] : null;
        }

        switch ($type) {
            case UserInterface::TYPE_ADULT:
                $user = new Adult();
                break;

            case UserInterface::TYPE_CHILD:
                $user = new Child();
                break;

            default:
                throw new \InvalidArgumentException('Invalid user type: ' . $type);
        }

        $user->exchangeArray($data);
        return $user;
    }
}
