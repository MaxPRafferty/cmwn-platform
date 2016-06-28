<?php

namespace Media;

/**
 * Interface MediaInterface
 */
interface MediaInterface
{
    const TYPE_EFFECT     = 'effect';
    const TYPE_SOUND      = 'sound';
    const TYPE_BACKGROUND = 'background';
    const TYPE_ITEM       = 'item';
    const TYPE_MESSAGE    = 'message';

    const CHECK_SHA1 = 'sha1';
    const CHECK_MD5  = 'md5';

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     * @return void
     */
    public function exchangeArray(array $array);

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy();

    /**
     * Gets the Check sum type of the file
     *
     * @return string
     */
    public function getCheckType();

    /**
     * Sets the checksum type
     *
     * @param $checkType
     * @return MediaInterface
     */
    public function setCheckType($checkType);

    /**
     * Gets the checksum value
     *
     * @return string
     */
    public function getCheckValue();

    /**
     * Sets the checksum value
     *
     * @param $checkValue
     * @return MediaInterface
     */
    public function setCheckValue($checkValue);

    /**
     * Gets the Id of the media
     *
     * @return string
     */
    public function getMediaId();

    /**
     * Sets the media Id
     *
     * @param string $mediaId
     * @return MediaInterface
     */
    public function setMediaId($mediaId);

    /**
     * Gets the FQDN URI of the media
     *
     * @return string
     */
    public function getSrc();

    /**
     * Sets the FQDN URI of the media
     *
     * @param string $url
     * @return MediaInterface
     */
    public function setSrc($url);

    /**
     * Gets the media type
     *
     * One of the types defined on this interface
     *
     * @return string
     */
    public function getAssetType();

    /**
     * Sets the media type
     *
     * @param string $type
     * @return MediaInterface
     */
    public function setAssetType($type);

    /**
     * Gets the Mime Type
     *
     * @return string
     */
    public function getMimeType();

    /**
     * Sets the mime type of the asset
     *
     * @param $mime
     * @return MediaInterface
     */
    public function setMimeType($mime);

    /**
     * Returns the name of the asset
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the name of the asset
     *
     * @param string $name
     */
    public function setName($name);
}
