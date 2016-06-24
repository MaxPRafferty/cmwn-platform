<?php

namespace Media\Service;

use Application\Exception\NotFoundException;
use Media\MediaInterface;

/**
 * Interface MediaServiceInterface
 */
interface MediaServiceInterface
{
    /**
     * @param $base
     * @return $this
     */
    public function setBaseUrl($base);

    /**
     * Imports information about an asset from the media server
     *
     * Current format of media data is
     * {
     *   media_id: "82dd5620-df30-11e5-a52e-0800274877349",
     *   type: "item",
     *   check: {
     *     type: "sha1",
     *     value: "82dd5620df3011e5a52e0800274877349"
     *   },
     *   mime_type: "image/png",
     *   src: "https://media.changemyworldnow.com/f/82dd5620-df30-11e5-a52e-0800274877349.png"
     * }
     *
     * @param $mediaId
     * @param $prototype
     *
     * @throws NotFoundException
     * @return MediaInterface|MediaCollection
     */
    public function importMediaData($mediaId, $prototype = null);

    /**
     * Imports a file to the specified path
     *
     * This will save the file locally and check that the file is valid
     *
     * @param $mediaId
     * @param $savePath
     *
     * @return bool
     */
    public function importFile($mediaId, $savePath);
}
