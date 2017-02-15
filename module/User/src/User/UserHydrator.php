<?php

namespace User;

use Zend\Hydrator\HydratorInterface;

/**
 * Class UserHydrator
 */
class UserHydrator implements HydratorInterface
{
    /**
     * @var null|UserInterface
     */
    protected $prototype;

    /**
     * UserHydrator constructor.
     *
     * @param null $prototype
     */
    public function __construct($prototype = null)
    {
        if ($prototype === null) {
            return;
        }

        if (!$prototype instanceof UserInterface) {
            throw new \InvalidArgumentException('This Hydrator can only hydrate Users');
        }

        if (!in_array(UserInterface::class, class_implements($prototype))) {
            throw new \InvalidArgumentException(sprintf(
                '%s  must implement %s',
                get_class($prototype),
                UserInterface::class
            ));
        }

        $this->prototype = $prototype;
    }

    /**
     * Extract values from an object
     *
     * @param  object $object
     *
     * @return array
     */
    public function extract($object)
    {
        if (!$object instanceof UserInterface) {
            throw new \InvalidArgumentException('This Hydrator can only extract Users');
        }

        return $object->getArrayCopy();
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     *
     * @return UserInterface
     */
    public function hydrate(array $data, $object)
    {
        $type = isset($data['type']) ? $data['type'] : $object;

        switch (true) {
            case $object instanceof UserInterface:
                // nothing user is what we want
                break;

            case $type === UserInterface::TYPE_ADULT:
                $object = new Adult();
                break;

            case $type === UserInterface::TYPE_CHILD:
                $object = new Child();
                break;

            case $this->prototype !== null:
                $object = new $this->prototype;
                break;

            default:
                throw new \InvalidArgumentException('Invalid user type: ' . $type);
        }

        $object->exchangeArray($data);

        return $object;
    }
}
