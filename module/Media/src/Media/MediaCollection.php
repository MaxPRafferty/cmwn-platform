<?php

namespace Media;

/**
 * Class MediaCollection
 */
class MediaCollection extends \ArrayObject
{
    /**
     * Ensures that we only have media objects in this collection
     *
     * @param mixed $index
     * @param mixed $media
     */
    public function offsetSet($index, $media)
    {
        if (!$media instanceof MediaInterface) {
            throw new \InvalidArgumentException('Only instances of MediaInterfaces can be set');
        }

        parent::offsetSet($media->getMediaId(), $media);
    }
}
