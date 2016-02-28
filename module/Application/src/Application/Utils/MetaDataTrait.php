<?php

namespace Application\Utils;

use Zend\Json\Json;

/**
 * Trait MetaDataTrait
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
trait MetaDataTrait
{
    /**
     * Meta data
     *
     * @var array
     */
    protected $meta = [];

    /**
     * Sets the meta data
     * @param array $meta
     */
    public function setMeta($meta = [])
    {
        if (!is_array($meta)) {
            try {
                $meta = Json::decode($meta, Json::TYPE_ARRAY);
            } catch (\Exception $jsonException) {
                $meta = [];
            }
        }

        $this->meta = $meta;
    }

    /**
     * Gets all the meta data
     *
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Add a value to meta data
     *
     * @param $key
     * @param $value
     */
    public function addToMeta($key, $value)
    {
        $this->meta[$key] = $value;
    }
}
