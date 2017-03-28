<?php

namespace Api\V1\Rest\SaveGame;

use Game\SaveGame;
use Game\SaveGameInterface;

/**
 * Represents a saved game data in the API
 *
 * @SWG\Definition(
 *     definition="SaveGameEntity",
 *     description="Represents a saved game data in the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_links",
 *         description="Hal Links for the game",
 *         allOf={
 *             @SWG\Schema(ref="#/definitions/SelfLink"),
 *         }
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/SaveGame"),
 *         @SWG\Schema(ref="#/definitions/SelfLink"),
 *     }
 * )
 */
class SaveGameEntity extends SaveGame implements SaveGameInterface
{

}
