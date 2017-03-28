<?php

namespace Api\V1\Rest\SaveGame;

use Zend\Paginator\Paginator;

/**
 * A Paginator for a collection of Save Games
 *
 * @SWG\Definition(
 *     description="A Collection of Game Saves from the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_embedded",
 *         description="A List of Game Save Entities",
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
class SaveGameCollection extends Paginator
{
}
