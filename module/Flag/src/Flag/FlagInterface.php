<?php

namespace Flag;

use User\UserInterface;

/**
 * Interface FlagInterface
 * @package Flag
 */
interface FlagInterface
{
    /**
     * Converts an Array into something that can be digested here
     *
     * @param array $array
     */
    public function exchangeArray(array $array);

    /**
     * Return this object represented as an array
     *
     * @return array
     */
    public function getArrayCopy();

    /**
     * @return string|null
     */
    public function getFlagId();

    /**
     * @param string $flagId
     */
    public function setFlagId($flagId);

    /**
     * @return UserInterface|null
     */
    public function getFlagger();

    /**
     * @param UserInterface|array|null $flagger
     */
    public function setFlagger($flagger);

    /**
     * @return UserInterface|null
     */
    public function getFlaggee();

    /**
     * @param UserInterface|array|null $flaggee
     */
    public function setFlaggee($flaggee);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getReason();
}
