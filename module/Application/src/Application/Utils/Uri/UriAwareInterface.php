<?php

namespace Application\Utils\Uri;

/**
 * An Interface that is aware of a uri
 *
 * @todo update to take in a PSR-7 UriInterface  Keeping out for now since Apigility Hal does not fully support
 */
interface UriAwareInterface
{
    /**
     * Sets a Uri to the object
     *
     * @param string $uri
     *
     * @return UriAwareInterface
     */
    public function setUri(string $uri): UriAwareInterface;

    /**
     * Gets the Uri from the object
     *
     * @return string
     */
    public function getUri(): string;
}
