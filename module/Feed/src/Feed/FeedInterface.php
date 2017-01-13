<?php

namespace Feed;

use User\UserInterface;

/**
 * Interface FeedInterface
 * @package Feed
 */
interface FeedInterface
{
    const TYPE_FRIEND = 'FRIEND';
    const TYPE_GAME = 'GAME';
    const TYPE_FLIP = 'FLIP';
    const TYPE_SKRIBBLE = 'SKRIBBLE';
    const FLIP_EARNED = 'You have earned a new flip';
    const FRIENDSHIP_MADE = 'You are now friends with';
    const SKRIBBLE_RECEIVED = 'You received a skribble';

    /**
     * @return string
     */
    public function getFeedId();

    /**
     * @param string $feedId
     */
    public function setFeedId($feedId);

    /**
     * @return UserInterface|string|null
     */
    public function getSender();

    /**
     * @param UserInterface|string|null $sender
     */
    public function setSender($sender);

    /**
     * @return String
     */
    public function getTitle();

    /**
     * @param String $title
     */
    public function setTitle($title);

    /**
     * @return mixed|String
     */
    public function getMessage();

    /**
     * @param mixed|String $message
     */
    public function setMessage($message);

    /**
     * @return string
     */
    public function getPriority();

    /**
     * @param string $priority
     */
    public function setPriority($priority);

    /**
     * @return \DateTime
     */
    public function getPosted();

    /**
     * @param \DateTime $posted
     */
    public function setPosted($posted);

    /**
     * @return int
     */
    public function getVisibility();

    /**
     * @param int $visibility
     */
    public function setVisibility($visibility);

    /**
     * @return String
     */
    public function getType();

    /**
     * @param String $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getTypeVersion();

    /**
     * @param string $typeVersion
     */
    public function setTypeVersion($typeVersion);

    /**
     * @return array
     */
    public function getArrayCopy();

    /**
     * @param array $array
     * @return mixed
     */
    public function exchangeArray($array = []);
}
