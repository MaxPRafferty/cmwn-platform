<?php

namespace Feed;

/**
 * A User Feed
 *
 * A User Feed essentially contains all the feed properties and has an additional property read flag
 * which lets us know if the feed is read by the user or not
 *
 * @SWG\Definition(
 *     definition="UserFeed",
 *     description="A user Feed is each user activity that is recorded to display it back to them",
 *     allOf={
 *          @SWG\Schema(ref="#/definitions/Feed"),
 *     },
 *     @SWG\Property(
 *          type="integer",
 *          format="int32",
 *          property="read_flag",
 *          description="The flag to determine if the user read this feed"
 *     ),
 * )
 */
interface UserFeedInterface extends FeedInterface
{
    /**
     * @return int
     */
    public function getReadFlag();

    /**
     * @param int $readFlag
     */
    public function setReadFlag($readFlag);
}
