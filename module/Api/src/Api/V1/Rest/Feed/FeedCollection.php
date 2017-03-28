<?php

namespace Api\V1\Rest\Feed;

use Zend\Paginator\Paginator;

/**
 * A Collection of Feed from the API
 *
 * @SWG\Definition(
 *     description="A Collection of Feed from the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_embedded",
 *         description="A List of Feed Entities",
 *         @SWG\Property(
 *             type="array",
 *             property="feed",
 *             description="A List of feed",
 *             @SWG\Items(ref="#/definitions/FeedEntity")
 *         )
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/Pagination")
 *     }
 * )
 */
class FeedCollection extends Paginator
{
}
