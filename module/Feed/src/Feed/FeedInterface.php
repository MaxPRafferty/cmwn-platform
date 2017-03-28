<?php

namespace Feed;

use User\UserInterface;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * A Feed
 *
 * A Feed is the activity that is recorded for each individual user.
 * There might also be a global feed that is common to all users
 *
 * @SWG\Definition(
 *     definition="Feed",
 *     description="A Feed is each user activity that is recorded to display it back to them",
 *     required={"feed_id", "type", "message", "title", "visibility"},
 *     allOf={
 *          @SWG\Schema(ref="#/definitions/DateCreated"),
 *          @SWG\Schema(ref="#/definitions/DateUpdated"),
 *          @SWG\Schema(ref="#/definitions/DateDeleted"),
 *          @SWG\Schema(ref="#/definitions/Meta"),
 *     },
 *     @SWG\Property(
 *          type="string",
 *          format="uuid",
 *          property="feed_id",
 *          description="The id of the feed"
 *     ),
 *     @SWG\Property(
 *          type="array",
 *          property="sender",
 *          description="sender of feed",
 *          @SWG\Items(ref="#/definitions/User")
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="title",
 *          description="Title of the feed item"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="message",
 *          description="Message of the feed item"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="priority",
 *          description="Priority of the feed item"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          format="date-time",
 *          property="posted",
 *          description="Posting date of the feed item"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="type",
 *          description="Type of the feed"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="type_version",
 *          description="Type of the feed"
 *     ),
 *     @SWG\Property(
 *          type="integer",
 *          format="int32",
 *          property="visibility",
 *          description="Visibility level of the feed"
 *     )
 * )
 */
interface FeedInterface extends ArraySerializableInterface
{
    const TYPE_FRIEND = 'FRIEND';
    const TYPE_GAME = 'GAME';
    const TYPE_FLIP = 'FLIP';
    const TYPE_SKRIBBLE = 'SKRIBBLE';

    const FEED_TYPE_VERSION = '1';

    const VISIBILITY_GLOBAL = 1;
    const VISIBILITY_FRIENDS = 2;
    const VISIBILITY_SELF = 4;

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
    public function setTypeVersion(string $typeVersion = null);

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
