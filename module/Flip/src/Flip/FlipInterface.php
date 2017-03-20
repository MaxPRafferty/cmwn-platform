<?php

namespace Flip;

use Feed\FeedableInterface;

/**
 * An Interface that defines a flip
 */
interface FlipInterface extends FeedableInterface
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
     * Designed to be fluent
     *
     * @param string $flipId
     *
     * @return FlipInterface
     */
    public function setFlipId(string $flipId): FlipInterface;

    /**
     * Gets the flip title
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Sets the title of the flip
     *
     * Designed to be fluent
     *
     * @param string $title
     *
     * @return FlipInterface
     */
    public function setTitle(string $title): FlipInterface;

    /**
     * Gets the description on how to earn the flip
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Sets a description on how to earn a flip
     *
     * Designed to be fluent
     *
     * @param string $description
     *
     * @return FlipInterface
     */
    public function setDescription(string $description): FlipInterface;
}
