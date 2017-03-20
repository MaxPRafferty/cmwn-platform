<?php

namespace Application\Utils\Uri;

/**
 * A Trait that resolved UriAwareInterface
 */
trait UriAwareTrait
{
    /**
     * @var the Uri
     */
    protected $uri = '';

    /**
     * Sets a Uri to the object
     *
     * @param string $uri
     *
     * @return UriAwareInterface
     */
    public function setUri(string $uri): UriAwareInterface
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Gets the Uri from the object
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }
}
