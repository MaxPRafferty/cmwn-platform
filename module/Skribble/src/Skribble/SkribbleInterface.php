<?php

namespace Skribble;

use Application\Utils\SoftDeleteInterface;
use Skribble\Rule\SkribbleRules;

/**
 * Interface SkribbleInterface
 */
interface SkribbleInterface extends SoftDeleteInterface
{
    const STATUS_COMPLETE     = 'COMPLETE';
    const STATUS_PROCESSING   = 'PROCESSING';
    const STATUS_NOT_COMPLETE = 'NOT_COMPLETE';
    const STATUS_ERROR        = 'ERROR';

    const CURRENT_VERSION     = '1';

    const SKRIBBLE_BASE_URL   = 'https://skribble.changemyworldnow.com/';

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     *2
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
     * @return string
     */
    public function getSkribbleId();

    /**
     * @param string $skirbbleId
     */
    public function setSkribbleId($skirbbleId);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getFriendTo();

    /**
     * @param string $friendTo
     */
    public function setFriendTo($friendTo);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     */
    public function setUrl($url);

    /**
     * @return int
     */
    public function getVersion();

    /**
     * @param int $version
     */
    public function setVersion($version);

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
    public function getCreated();

    /**
     * @param \DateTime|string|null $created
     * @return $this
     */
    public function setCreated($created);

    /**
     * @return string
     */
    public function getCreatedBy();

    /**
     * @param string $createdBy
     */
    public function setCreatedBy($createdBy);

    /**
     * @return SkribbleRules
     */
    public function getRules();

    /**
     * @param SkribbleRules|string|array $rules
     */
    public function setRules($rules);

    /**
     * @return boolean
     */
    public function isRead();

    /**
     * @param boolean $read
     */
    public function setRead($read);
}
