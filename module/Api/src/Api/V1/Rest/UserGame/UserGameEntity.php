<?php

namespace Api\V1\Rest\UserGame;

use Game\Game;
use Game\GameInterface;

/**
 * A UserGame Entity represents the game through the API
 *
 * @SWG\Definition(
 *     definition="UserGameEntity",
 *     description="A UserGame Entity represents the game through the API",
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
class UserGameEntity extends Game implements GameInterface
{
}
