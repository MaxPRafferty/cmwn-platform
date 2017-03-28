<?php

namespace Api\V1\Rest\FeedUser;

use Feed\UserFeed;
use Feed\UserFeedInterface;

/**
 * A Feed User Entity represents the feed through the API
 *
 * @SWG\Definition(
 *     definition="FeedUserEntity",
 *     description="A feed user Entity represents the user feed through the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_links",
 *         description="Links the user feed might have",
 *         allOf={
 *             @SWG\Schema(ref="#/definitions/SelfLink"),
 *         }
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/UserFeed"),
 *         @SWG\Schema(ref="#/definitions/SelfLink"),
 *     }
 * )
 */
class FeedUserEntity extends UserFeed implements UserFeedInterface
{
}
