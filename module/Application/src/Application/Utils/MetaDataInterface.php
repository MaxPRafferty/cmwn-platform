<?php

namespace Application\Utils;

/**
 * An Interface that allows an object to have a metadata
 *
 * @SWG\Definition(
 *     definition="Meta",
 *     description="Meta data for the entity",
 *     @SWG\Property(
 *         type="string",
 *         format="array",
 *         property="meta",
 *         description="Meta data of the entity"
 *     )
 * )
 */
interface MetaDataInterface
{
    /**
     * Gets all the meta data
     *
     * @return array
     */
    public function getMeta();

    /**
     * Add a value to meta data
     *
     * @param $key
     * @param $value
     */
    public function addToMeta($key, $value);

    /**
     * Sets the meta data
     * @param array $meta
     */
    public function setMeta($meta = []);
}
