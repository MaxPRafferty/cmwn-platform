<?php

namespace Api\V1\Rest\Feed;

use Feed\Feed;
use Feed\FeedInterface;

/**
 * A Feed Entity represents the feed through the API
 *
 * @SWG\Definition(
 *     definition="FeedEntity",
 *     description="A feed Entity represents the feed through the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_links",
 *         description="Links the feed might have",
 *         allOf={
 *             @SWG\Schema(ref="#/definitions/SelfLink"),
 *         }
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/Feed"),
 *         @SWG\Schema(ref="#/definitions/SelfLink"),
 *     }
 * )
 */
class FeedEntity extends Feed implements FeedInterface
{
}
