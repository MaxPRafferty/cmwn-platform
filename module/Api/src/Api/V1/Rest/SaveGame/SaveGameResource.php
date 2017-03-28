<?php

namespace Api\V1\Rest\SaveGame;

use Game\Game;
use Game\Service\SaveGameServiceInterface;
use Game\Service\UserGameServiceInterface;
use User\Service\UserServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * API Resource that deals with saving games
 */
class SaveGameResource extends AbstractResourceListener
{
    /**
     * @var SaveGameServiceInterface
     */
    protected $saveService;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var UserGameServiceInterface
     */
    protected $gameService;

    /**
     * SaveGameResource constructor.
     *
     * @param SaveGameServiceInterface $saveGameService
     * @param UserServiceInterface $userService
     * @param UserGameServiceInterface $gameService
     */
    public function __construct(
        SaveGameServiceInterface $saveGameService,
        UserServiceInterface $userService,
        UserGameServiceInterface $gameService
    ) {
        $this->saveService = $saveGameService;
        $this->userService = $userService;
        $this->gameService = $gameService;
    }

    /**
     * Fetch All Saved games for a user
     *
     * The user must be allowed access to the game and the user.
     *
     * @SWG\Get(path="/game",
     *   tags={"game"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="User Id to update",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     minimum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="The Requested Data",
     *     @SWG\Schema(ref="#/definitions/SaveGameCollection")
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
        // 404 if user is not found
        $user = $this->userService->fetchUser(
            $this->getEvent()->getRouteParam('user')
        );

        return new SaveGameCollection(
            $this->saveService->fetchAllSaveGamesForUser($user, null, new SaveGameEntity())
        );
    }

    /**
     * Fetch the saved data for a game
     *
     * The user must be allowed access to the game and the user.
     *
     * @SWG\Get(path="/game/{game_id}",
     *   tags={"game"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="game_id",
     *     in="path",
     *     description="Game Id to update",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     minimum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="User Id to update",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     minimum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="The Requested Data",
     *     @SWG\Schema(ref="#/definitions/SaveGameEntity")
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="validation failed",
     *     @SWG\Schema(ref="#/definitions/ValidationError")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to update a game",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     *
     * @param  string $gameId
     *
     * @return ApiProblem|mixed
     */
    public function fetch($gameId)
    {
        // 404 if user is not found
        $user = $this->userService->fetchUser(
            $this->getEvent()->getRouteParam('user')
        );

        $game = new Game();
        $game->setGameId($gameId);

        // 404 if game is not found
        $game = $this->gameService->fetchGameForUser($user, $game);

        return $this->saveService->fetchSaveGameForUser($user, $game, null, new SaveGameEntity());
    }

    /**
     * Save Data to a game
     *
     * The user must be allowed access to the game and the user.
     *
     * @SWG\Put(path="/game/{game_id}",
     *   tags={"game"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="game_id",
     *     in="path",
     *     description="Game Id to update",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     minimum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="User Id to update",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     minimum=1.0
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Game data",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/SaveGame")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(ref="#/definitions/SaveGameEntity")
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="validation failed",
     *     @SWG\Schema(ref="#/definitions/ValidationError")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to update a game",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  string $gameId
     * @param  mixed $data
     *
     * @return ApiProblem|mixed
     */
    public function update($gameId, $data)
    {
        // 404 if user is not found
        $user = $this->userService->fetchUser(
            $this->getEvent()->getRouteParam('user')
        );

        $game = new Game();
        $game->setGameId($gameId);

        // 404 if game is not found
        $game = $this->gameService->fetchGameForUser($user, $game);

        $saveGame = new SaveGameEntity();
        $saveGame->setData($this->getInputFilter()->getValue('data'));
        $saveGame->setVersion($this->getInputFilter()->getValue('version'));
        $saveGame->setUserIdFromUser($user);
        $saveGame->setGameIdFromGame($game);

        if ($this->saveService->saveGame($saveGame)) {
            return $saveGame;
        }

        return new ApiProblem(500, 'Failed to save game data');
    }

    /**
     * Delete a game
     *
     * A fetch is done first to ensure the user has access to a game.
     *
     * @SWG\Delete(path="/user/{user_id}/game{game_id}",
     *   tags={"game"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="game_id",
     *     in="path",
     *     description="Id of the game to delete data for",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     minimum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="Id of the user to delete data for",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     minimum=1.0
     *   ),
     *   @SWG\Response(
     *     response=204,
     *     description="User Data for game was deleted",
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="User or Game not found",
     *     @SWG\Schema(ref="#/definitions/NotFoundError")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to delete data for this user or access game",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=500,
     *     description="Problem occurred during execution",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  string $gameId
     *
     * @return ApiProblem|bool
     */
    public function delete($gameId)
    {
        // 404 if user is not found
        $user = $this->userService->fetchUser(
            $this->getEvent()->getRouteParam('user')
        );

        $game = new Game();
        $game->setGameId($gameId);

        // 404 if game is not found
        $game = $this->gameService->fetchGameForUser($user, $game);

        if ($this->saveService->deleteSaveForUser($user, $game)) {
            return true;
        }

        return new ApiProblem(500, 'Failed to delete saved game data');
    }
}
