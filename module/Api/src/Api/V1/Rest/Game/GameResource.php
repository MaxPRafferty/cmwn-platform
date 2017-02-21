<?php

namespace Api\V1\Rest\Game;

use Game\Game;
use Game\Service\GameServiceInterface;
use Zend\Paginator\Adapter\DbSelect;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class GameResource
 * @package Api\V1\Rest\Game
 */
class GameResource extends AbstractResourceListener
{
    /**
     * @var GameServiceInterface
     */
    protected $service;

    /**
     * UserResource constructor.
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
     *     @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref="#/definitions/GameCollection")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Game not found",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/NotFoundError")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/Error")
     *     )
     *   )
     * )
     * @param  array $params
     *
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        /** @var DbSelect $games */
        $deleted = $params['deleted'] === 'true' ?? false;
        $games = $this->service->fetchAll(null, new GameEntity(), $deleted);
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
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/NotFoundError")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/Error")
     *     )
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
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/GameEntity")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="Validation failed",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/ValidationError")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to update a game",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/Error")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/Error")
     *     )
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
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/GameEntity")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="validation failed",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/ValidationError")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to update a game",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/Error")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/Error")
     *     )
     *   )
     * )
     * @param  mixed $gameId
     * @param  mixed $data
     *
     * @return ApiProblem|mixed
     */
    public function update($gameId, $data)
    {
        $data = $this->getInputFilter()->getValues();
        $game = $this->service->fetchGame($gameId);

        $data = array_merge($game->getArrayCopy(), $data);

        if ($data['undelete']) {
            unset($data['deleted']);
        }

        $game->exchangeArray($data);
        $this->service->saveGame($game);

        return $game;
    }

    /**
     * Delete a game
     *
     * A fetch is done first to ensure the user has access to a game.  By default games are soft deleted unless
     * the "hard" parameter is set in the query.  The authenticated user will get a 403 if the they are not allowed
     * to hard delete a game
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
     *     response=200,
     *     description="Game was deleted",
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Game not found",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/NotFoundError")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to delete or access game",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/Error")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/Error")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=500,
     *     description="Problem occured during execution",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/Error")
     *     )
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
