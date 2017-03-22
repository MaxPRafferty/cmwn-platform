<?php

namespace Api\V1\Rest\Game;

use Game\Game;
use Game\Service\GameServiceInterface;
use Zend\Paginator\Adapter\DbSelect;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * A Resource that handles the game API
 */
class GameResource extends AbstractResourceListener
{
    /**
     * @var GameServiceInterface
     */
    protected $service;

    /**
     * GameResource constructor.
     *
     * @param GameServiceInterface $service
     */
    public function __construct(GameServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Fetches multiple games the authenticated user has access too
     *
     * A User can request all games or all the child games descending from parent.  Empty results are returned if the
     * user is not allowed access to a parent
     *
     * @SWG\Get(path="/game",
     *   tags={"game"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="deleted",
     *     in="query",
     *     description="Flag to fetch deleted games",
     *     type="boolean",
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number to fetch",
     *     type="integer",
     *     format="int32",
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="per_page",
     *     in="query",
     *     description="Number of games on each page",
     *     type="integer",
     *     format="int32",
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Paged games",
     *     @SWG\Schema(ref="#/definitions/GameCollection")
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Game not found",
     *     @SWG\Schema(ref="#/definitions/NotFoundError")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  array $params
     *
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        $params = (array)$params;
        /** @var DbSelect $games */
        $deleted = (bool)($params['deleted'] ?? false);
        unset($params['deleted']);
        $games = $this->service->fetchAll($params, new GameEntity(), $deleted);

        return new GameCollection($games);
    }

    /**
     * Fetch data for a game
     *
     * Fetch the data for a game if the authenticated user is allowed access.
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
     *     description="Game Id to fetch",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="The requested game",
     *     @SWG\Schema(ref="#/definitions/GameCollection")
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Game not found",
     *     @SWG\Schema(ref="#/definitions/NotFoundError")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  mixed $gameId
     *
     * @return ApiProblem|GameEntity
     */
    public function fetch($gameId)
    {
        $game = $this->service->fetchGame($gameId);

        return new GameEntity($game->getArrayCopy());
    }

    /**
     * Create a new game
     *
     * The authenticated user must be allowed to create a new game in the system
     *
     * @SWG\Post(path="/game",
     *   tags={"game"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Game data",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/Game")
     *   ),
     *   @SWG\Response(
     *     response=201,
     *     description="Game was created",
     *     @SWG\Schema(ref="#/definitions/GameEntity")
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="Validation failed",
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
     * @param  mixed $data
     *
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $game = new Game($this->getInputFilter()->getValues());
        $this->service->createGame($game);

        return new GameEntity($game->getArrayCopy());
    }

    /**
     * Update a game
     *
     * The user must be allowed access to the game and be allowed to edit games.  403 is returned if the user is not
     * allowed access to update the game. 404 is returned if the game is not found or the user is not allowed access
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
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Game data",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/Game")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(ref="#/definitions/GameEntity")
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
        $data       = $this->getInputFilter()->getValues();
        $game       = $this->fetch($gameId);
        $data       = array_merge($game->getArrayCopy(), $data);

        $game->exchangeArray($data);
        $this->service->saveGame($game, true);

        return $game;
    }

    /**
     * Delete a game
     *
     * A fetch is done first to ensure the user has access to a game.
     *
     * @SWG\Delete(path="/game/{game_id}",
     *   tags={"game"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="game_id",
     *     in="path",
     *     description="Game Id to deleted",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=204,
     *     description="Game was deleted",
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Game not found",
     *     @SWG\Schema(ref="#/definitions/NotFoundError")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to delete or access game",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=500,
     *     description="Problem occured during execution",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  string $gameId
     *
     * @return ApiProblem|mixed
     */
    public function delete($gameId)
    {
        $game = $this->fetch($gameId);
        if ($this->service->deleteGame($game)) {
            return true;
        }

        return new ApiProblem(500, 'Problem deleting the game');
    }
}
