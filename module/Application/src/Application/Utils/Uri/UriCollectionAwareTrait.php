<?php

namespace Application\Utils\Uri;

use Application\Exception\RuntimeException;
use Zend\Json\Json;

/**
 * A Trait that helps satisfy UriCollectionAwareInterface
 */
trait UriCollectionAwareTrait
{
    protected $uris = [];

    /**
     * Sets all the URIS for the object
     *
     * @param array|string $uris An Array of URI's or Json Encoded Object of URI's
     *
     * @return UriCollectionAwareInterface
     */
    public function setUris($uris): UriCollectionAwareInterface
    {
        $uris       = is_string($uris) ? Json::decode($uris, Json::TYPE_ARRAY) : $uris;
        $uris       = !is_array($uris) ? [] : $uris;
        $this->uris = $uris;

        return $this;
    }

    /**
     * Gets all the URI's set for the object
     *
     * @return string[]
     */
    public function getUris(): array
    {
        return $this->uris;
    }

    /**
     * Gets a URI
     *
     * Returns an empty string if the URI is not set
     *
     * @param string $which the uri you wish to get
     *
     * @return string
     */
    public function getUri(string $which): string
    {
        return $this->uris[$which] ?? '';
    }

    /**
     * Adds a new type of uri to the object
     *
     * @param string $which
     * @param string $uri
     *
     * @return UriCollectionAwareInterface
     */
    public function addUri(string $which, string $uri): UriCollectionAwareInterface
    {
        $this->uris[$which] = $uri;

        return $this;
    }
}
