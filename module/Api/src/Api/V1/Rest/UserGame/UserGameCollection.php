<?php

namespace Api\V1\Rest\UserGame;

use Zend\Paginator\Paginator;

/**
 * A Collection of Games from the API
 *
 * @SWG\Definition(
 *     description="A Collection of Games for users from the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_embedded",
 *         description="A List of UserGame Entities",
 *         @SWG\Property(
 *             type="array",
 *             property="games",
 *             description="A List of user games",
 *             @SWG\Items(ref="#/definitions/UserGameEntity")
 *         )
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/Pagination")
 *     }
 * )
 */
class UserGameCollection extends Paginator
{
}
