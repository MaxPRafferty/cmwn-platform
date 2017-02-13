<?php

namespace Application\Utils\Meta;

/**
 * An interface that allows an object to have meta data
 */
interface MetaDataInterface
{
    /**
     * Sets the meta data
     *
     * @param array|string $meta SHOULD expect an array or a JSON string
     */
    public function setMeta($meta = []);

    /**
     * Gets all the meta data
     *
     * @return array
     */
    public function getMeta(): array;

    /**
     * Appends a value to the meta data
     *
     * @param $key
     * @param $value
     */
    public function addToMeta($key, $value);
}
