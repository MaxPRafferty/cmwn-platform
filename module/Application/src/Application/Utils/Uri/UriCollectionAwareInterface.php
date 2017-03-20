<?php

namespace Application\Utils\Uri;

/**
 * An Interface that is aware of multiple types of URIS
 */
interface UriCollectionAwareInterface
{
    /**
     * Sets all the URIS for the object
     *
     * @param array|string $uris An Array of URI's or Json Encoded Object of URI's
     *
     * @return UriCollectionAwareInterface
     */
    public function setUris($uris): UriCollectionAwareInterface;

    /**
     * Gets all the URI's set for the object
     *
     * @return string[]
     */
    public function getUris(): array;

    /**
     * Gets a URI
     *
     * @param string $which the uri you wish to get
     *
     * @return string
     */
    public function getUri(string $which): string;

    /**
     * Adds a new type of uri to the object
     *
     * @param string $which
     * @param string $uri
     *
     * @return UriCollectionAwareInterface
     */
    public function addUri(string $which, string $uri): UriCollectionAwareInterface;
}
