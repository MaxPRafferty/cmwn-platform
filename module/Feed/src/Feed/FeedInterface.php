<?php

namespace Feed;

use User\UserInterface;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Interface FeedInterface
 * @package Feed
 */
interface FeedInterface extends ArraySerializableInterface
{
    const TYPE_FRIEND = 'FRIEND';
    const TYPE_GAME = 'GAME';
    const TYPE_FLIP = 'FLIP';
    const TYPE_SKRIBBLE = 'SKRIBBLE';
    const TITLE_FLIP_EARNED = 'You have earned a new flip';
    const TITLE_FRIENDSHIP_MADE = 'You are now friends with';
    const TITLE_SKRIBBLE_RECEIVED = 'You received a skribble';
    const TITLE_GAME_ADDED = 'New Game is Added';
    const MESSAGE_FLIP_EARNED = 'Flip Earned';
    const MESSAGE_FRIENDSHIP_MADE = 'Friendship Made';
    const MESSAGE_SKRIBBLE_RECEIVED = 'Skribble Received';
    const MESSAGE_GAME_ADDED = 'New Game Added';
    const VISIBILITY_SELF = 4;
    const VISIBILITY_FRIENDS = 2;
    const VISIBILITY_GLOBAL = 0;

    /**
     * @return string
     */
    public function getFeedId() : string;

    /**
     * @param string $feedId
     */
    public function setFeedId(string $feedId = null);

    /**
     * @return UserInterface|string|null
     */
    public function getSender();

    /**
     * @param UserInterface|string|null $sender
     */
    public function setSender($sender);

    /**
     * @return string
     */
    public function getTitle() : string;

    /**
     * @param string $title
     */
    public function setTitle(string $title = null);

    /**
     * @return string
     */
    public function getMessage() : string;

    /**
     * @param mixed|String $message
     */
    public function setMessage(string $message = null);

    /**
     * @return string
     */
    public function getPriority() : string;

    /**
     * @param string $priority
     */
    public function setPriority(string $priority = null);

    /**
     * @return string
     */
    public function getPosted() : string;

    /**
     * @param string $posted
     */
    public function setPosted(string $posted = null);

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
    public function getType() : string;

    /**
     * @param String $type
     */
    public function setType(string $type = null);

    /**
     * @return string
     */
    public function getTypeVersion() : string;

    /**
     * @param string $typeVersion
     */
    public function setTypeVersion(string $typeVersion = 1);

    /**
     * @return array
     */
    public function getArrayCopy() : array;

    /**
     * @param array $array
     * @return mixed
     */
    public function exchangeArray(array $array = []);
}
