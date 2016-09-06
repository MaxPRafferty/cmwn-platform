<?php

namespace Asset;

/**
 * Interface ImageInterface
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
interface ImageInterface
{
    const IMAGE_APPROVED = 1;
    const IMAGE_PENDING  = 0;
    const IMAGE_REJECTED = -1;

    /**
     * Transform a status to its value
     *
     * @param $status
     * @return int
     */
    public static function statusToNumber($status);

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
     * @return boolean
     */
    public function isModerated();

    /**
     * @return boolean
     */
    public function isApproved();

    /**
     * @param boolean $moderated
     * @return Image
     */
    public function setModerated($moderated);

    /**
     * @return string
     */
    public function getImageId();

    /**
     * @param string $imageId
     * @return Image
     */
    public function setImageId($imageId);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     * @return Image
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return Image
     */
    public function setType($type);

    /**
     * @return \DateTime|null
     */
    public function getUpdated();

    /**
     * @param \DateTime|null $updated
     * @return $this
     */
    public function setUpdated($updated);
    /**
     * @return \DateTime|null
     */
    public function getDeleted();

    /**
     * @param \DateTime|string|null $deleted
     * @return $this
     */
    public function setDeleted($deleted);

    /**
     * @return bool
     */
    public function isDeleted();

    /**
     * @return bool
     */
    public function isRejected();

    /**
     * @return \DateTime|null
     */
    public function getCreated();

    /**
     * @param \DateTime|string|null $created
     * @return $this
     */
    public function setCreated($created);

    /**
     * @return integer
     */
    public function getModerationStatus();

    /**
     * @param integer $moderationStatus
     */
    public function setModerationStatus($moderationStatus);
}
