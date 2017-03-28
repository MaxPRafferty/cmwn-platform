<?php


namespace Api\V1\Rest\FeedUser;

use Zend\Paginator\Paginator;

/**
 * A Collection of Feed from the API
 *
 * @SWG\Definition(
 *     description="A Collection of User Feed from the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_embedded",
 *         description="A List of Feed User Entities",
 *         @SWG\Property(
 *             type="array",
 *             property="feed",
 *             description="A List of feed",
 *             @SWG\Items(ref="#/definitions/FeedUserEntity")
 *         )
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/Pagination")
 *     }
 * )
 */
class FeedUserCollection extends Paginator
{
}
