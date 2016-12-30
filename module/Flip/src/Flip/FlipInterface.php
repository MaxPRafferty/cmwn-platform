<?php

namespace Flip;

/**
 * An Interface that defines a flip
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
     */
    public function exchangeArray(array $array);

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy(): array;

    /**
     * Gets the flip Id
     *
     * @return string
     */
    public function getFlipId(): string;

    /**
     * Sets the Flip Id
     *
     * @param string $flipId
     *
     * @return Flip
     */
    public function setFlipId(string $flipId);

    /**
     * Gets the flip title
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Sets the title of the flip
     *
     * @param string $title
     *
     * @return Flip
     */
    public function setTitle(string $title);

    /**
     * Gets the description on how to earn the flip
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Sets a description on how to earn a flip
     *
     * @param string $description
     *
     * @return Flip
     */
    public function setDescription(string $description);
}
