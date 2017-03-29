<?php

namespace Api\V1\Rest\GameData;

use Game\Service\SaveGameServiceInterface;
use ZF\Rest\AbstractResourceListener;

/**
 * A resource that fetches all the game data for all games or just for one game across all users
 */
class GameDataResource extends AbstractResourceListener
{
    /**
     * @var SaveGameServiceInterface
     */
    protected $saveGameService;

    /**
     * GameDataResource constructor.
     *
     * @param SaveGameServiceInterface $saveGameService
     */
    public function __construct(SaveGameServiceInterface $saveGameService)
    {
        $this->saveGameService = $saveGameService;
    }

    /**
     * Fetch All Saved games for a game
     *
     * The user must be allowed access to the game and the user.
     *
     * @SWG\Get(path="/game-data/{game_id}",
     *   tags={"game"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *     @SWG\Parameter(
     *     name="game_id",
     *     in="path",
     *     description="Game Id",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     minimum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="The Requested Data",
     *     @SWG\Schema(ref="#/definitions/GameDataCollection")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized fetch games for this user",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     *
     * @param string $gameId
     *
     * @return ApiProblem|mixed
     */
    public function fetch($gameId)
    {
        return new GameDataCollection(
            $this->saveGameService->fetchAllSaveGameData(['game_id' => $gameId], new GameDataEntity())
        );
    }

    /**
     * Fetch All Saved games
     *
     * The user must be allowed access to the game and the user.
     *
     * @SWG\Get(path="/game-data",
     *   tags={"game"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="The Requested Data",
     *     @SWG\Schema(ref="#/definitions/GameDataCollection")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized fetch games for this user",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     *
     * @param $params
     *
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        return new GameDataCollection(
            $this->saveGameService->fetchAllSaveGameData(null, new GameDataEntity())
        );
    }
}
