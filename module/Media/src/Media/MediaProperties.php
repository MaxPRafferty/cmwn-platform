<?php

namespace Media;

/**
 * Class MediaProperties
 * @todo Allow certain properties based on media type when better DAM is in place
 *
 * @property bool $can_overlap
 */
class MediaProperties
{
    const CAN_OVERLAP = 'can_overlap';

    /**
     * Values of the properties
     *
     * @var array
     */
    protected $properties = [
        self::CAN_OVERLAP => false,
    ];

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->getProperty($property);
    }

    /**
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        $this->setProperty($property, $value);
    }

    /**
     * Sets a property
     *
     * @param $property
     * @param $value
     */
    public function setProperty($property, $value)
    {
        if (!$this->isProperty($property)) {
            throw new \OutOfRangeException(
                sprintf('Invalid property: "%s"', $property)
            );
        }

        // TODO cast value
        // TODO validate value
        $this->properties[$property] = $value;
    }

    /**
     * Gets the property
     *
     * @param $property
     * @return mixed
     */
    public function getProperty($property)
    {
        if (!$this->isProperty($property)) {
            throw new \OutOfRangeException(
                sprintf('Cannot access property: "%s"', $property)
            );
        }

        return $this->properties[$property];
    }

    /**
     * Tests if a property is valid
     *
     * @param $property
     * @return bool
     */
    public function isProperty($property)
    {
        return isset($this->properties[$property]);
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return $this->properties;
    }
}
