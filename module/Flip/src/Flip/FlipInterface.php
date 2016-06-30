<?php

namespace Flip;

/**
 * Interface FlipInterface
 */
interface FlipInterface
{

    /**
     * @return string
     */
    public function __toString();

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     * @return void
     */
    public function exchangeArray(array $array);

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy();

    /**
     * Gets the flip Id
     *
     * @return string
     */
    public function getFlipId();

    /**
     * Sets the Flip Id
     *
     * @param string $flipId
     * @return Flip
     */
    public function setFlipId($flipId);

    /**
     * Gets the flip title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Sets the title of the flip
     *
     * @param string $title
     * @return Flip
     */
    public function setTitle($title);

    /**
     * Gets the description of the flip
     *
     * @return string
     */
    public function getDescription();

    /**
     * Gets the flip Description
     *
     * @param string $description
     * @return Flip
     */
    public function setDescription($description);
}
