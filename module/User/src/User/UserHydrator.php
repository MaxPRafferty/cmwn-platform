<?php

namespace User;

use Zend\Hydrator\HydratorInterface;
use Zend\Stdlib\Extractor\ExtractionInterface;

/**
 * Class UserHydrator
 */
class UserHydrator implements HydratorInterface, ExtractionInterface
{
    /**
     * @var null|UserInterface
     */
    protected $prototype;

    public function __construct($prototype = null)
    {
        if ($prototype === null) {
            return;
        }

        if (!$prototype instanceof UserInterface) {
            throw new \InvalidArgumentException('This Hydrator can only hydrate Users');
        }

        $this->prototype = $prototype;
    }

    /**
     * Extract values from an object
     *
     * @param  object $object
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
     * @return UserInterface
     */
    public function hydrate(array $data, $object)
    {
        if ($this->prototype === null) {
            return StaticUserFactory::createUser($data);
        }

        $object = clone $this->prototype;
        $object->exchangeArray($data);
        return $object;
    }
}
