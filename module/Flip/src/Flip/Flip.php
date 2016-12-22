<?php

namespace Flip;

use Zend\Filter\StaticFilter;
use Zend\Filter\Word\UnderscoreToCamelCase;

/**
 * Class Flip
 */
class Flip implements FlipInterface
{
    /**
     * @var string
     */
    protected $flipId;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * Flip constructor.
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        if ($options !== null) {
            $this->exchangeArray($options);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getTitle();
    }

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     * @return void
     */
    public function exchangeArray(array $array)
    {
        $defaults = [
            'flip_id'     => null,
            'title'       => null,
            'description' => null,
        ];

        $array = array_merge($defaults, $array);

        foreach ($array as $key => $value) {
            $method = 'set' . ucfirst(StaticFilter::execute($key, UnderscoreToCamelCase::class));
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'flip_id'     => $this->getFlipId(),
            'title'       => $this->getTitle(),
            'description' => $this->getDescription(),
        ];
    }
    /**
     * Gets the flip Id
     *
     * @return string
     */
    public function getFlipId()
    {
        return $this->flipId;
    }

    /**
     * Sets the Flip Id
     *
     * @param string $flipId
     * @return Flip
     */
    public function setFlipId($flipId)
    {
        $this->flipId = $flipId;

        return $this;
    }

    /**
     * Gets the flip title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title of the flip
     *
     * @param string $title
     * @return Flip
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the description of the flip
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Gets the flip Description
     *
     * @param string $description
     * @return Flip
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
