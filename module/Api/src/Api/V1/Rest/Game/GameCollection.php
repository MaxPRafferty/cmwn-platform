<?php

namespace Api\V1\Rest\Game;

use Zend\Paginator\Paginator;

/**
 * A Collection of Games from the API
 *
 * @SWG\Definition(
 *     description="A Collection of Games from the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_embedded",
 *         description="A List of Game Entities",
 *         @SWG\Property(
 *             type="array",
 *             property="games",
 *             description="A List of games",
 *             @SWG\Items(ref="#/definitions/GameEntity")
 *         )
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/Pagination")
 *     }
 * )
 */
class GameCollection extends Paginator
{
}
