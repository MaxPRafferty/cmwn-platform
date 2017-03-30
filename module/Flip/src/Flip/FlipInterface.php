<?php

namespace Flip;

use Application\Utils\Uri\UriCollectionAwareInterface;

/**
 * An Interface that defines a flip
 */
interface FlipInterface extends UriCollectionAwareInterface
{
    const IMAGE_DEFAULT  = 'default';
    const IMAGE_EARNED   = 'earned';
    const IMAGE_UNEARNED = 'unearned';
    const IMAGE_COIN     = 'coin';
    const IMAGE_STATIC   = 'static';

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
     * Return an array representation of the flip
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
     * @param string $description
     *
     * @return FlipInterface
     */
    public function setDescription(string $description): FlipInterface;
}
