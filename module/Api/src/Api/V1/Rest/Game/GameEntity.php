<?php

namespace Api\V1\Rest\Game;

use Game\Game;
use Game\GameInterface;

/**
 * A Game Entity represents the game through the API
 *
 * @SWG\Definition(
 *     definition="GameEntity",
 *     description="A Game Entity represents the game through the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_links",
 *         description="Links the game might have",
 *         allOf={
 *             @SWG\Schema(ref="#/definitions/SelfLink"),
 *         }
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/Game"),
 *         @SWG\Schema(ref="#/definitions/SelfLink"),
 *     }
 * )
 */
class GameEntity extends Game implements GameInterface
{
}
