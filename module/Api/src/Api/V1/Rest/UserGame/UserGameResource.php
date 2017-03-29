<?php

namespace Api\V1\Rest\UserGame;

use Game\GameInterface;
use Game\Service\GameServiceInterface;
use Game\Service\UserGameService;
use Game\Service\UserGameServiceInterface;
use User\Service\UserServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Allows attaching or detaching games to a user
 */
class UserGameResource extends AbstractResourceListener
{
    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var GameServiceInterface
     */
    protected $gameService;

    /**
     * @var UserGameService
     */
    protected $userGameService;


    protected function getUser()
    {

    }
    /**
     * UserGameResource constructor.
     *
     * @param UserServiceInterface $userService
     * @param GameServiceInterface $gameService
     * @param UserGameServiceInterface $userGameService
     */
    public function __construct(
        UserServiceInterface $userService,
        GameServiceInterface $gameService,
        UserGameServiceInterface $userGameService
    ) {
        $this->userGameService = $userGameService;
        $this->userService     = $userService;
        $this->gameService     = $gameService;
    }

    /**
     * Attach a game to a user
     *
     * The authenticated user must be allowed to attach a game to a user in the system
     *
     * @SWG\Post(path="/user/{user_id}/game/{game_id}",
     *   tags={"user", "game"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="User Id of the user",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0
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
     *     response=201,
     *     description="Game attached to user",
     *     @SWG\Schema(ref="#/definitions/UserGameEntity")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to attach game",
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
     * @return ApiProblem|GameEntity|GameInterface
     */
    public function create($data)
    {
        $userId = $this->getEvent()->getRouteParam('user_id');
        $gameId = $this->getEvent()->getRouteParam('game_id');

        // 404 if the user is not found
        $user = $this->userService->fetchUser($userId);

        // 404 if the game is not found
        $game = $this->gameService->fetchGame($gameId, new UserGameEntity());

        if ($this->userGameService->attachGameToUser($user, $game)) {
            return $game;
        }

        return new ApiProblem(500, 'failed to add the game to user');
    }

    /**
     * Detach a game from a user
     *
     * A fetch is done first to ensure the user has access to a game. The authenticated user will get a 403
     * if the they are not allowed to detach a game from a user
     *
     * @SWG\Delete(path="/user/{user_id}/game/{game_id}",
     *   tags={"user", "game"},
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
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="User Id of the user",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=204,
     *     description="Game was detached from user",
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Game not found",
     *     @SWG\Schema(ref="#/definitions/NotFoundError")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to detach or access game",
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
     * @return ApiProblem|bool
     */
    public function delete($gameId)
    {
        $userId = $this->getEvent()->getRouteParam('user_id');

        // 404 if the user is not found
        $user = $this->userService->fetchUser($userId);

        // 404 if the game is not found
        $game = $this->gameService->fetchGame($gameId);

        // detach the game
        if ($this->userGameService->detachGameForUser($user, $game)) {
            return true;
        }

        return new ApiProblem(500, 'failed to remove game from user');
    }

    /**
     * Fetches multiple games the requested user has access too
     *
     * A User can request all games or all the child games descending from parent.  Empty results are returned if the
     * user is not allowed access to a parent
     *
     * @SWG\Get(path="/user/{user_id}/game",
     *   tags={"user", "game"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="User Id of the user",
     *     required=true,
     *     type="string",
     *     format="uuid",
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
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  array $params
     *
     * @return ApiProblem|UserGameCollection
     */
    public function fetchAll($params = [])
    {
        $params = (array) $params;
        unset($params['page'], $params['per_page']);

        // 404 if the user is not found
        $user = $this->userService->fetchUser(
            $this->getEvent()->getRouteParam('user_id')
        );

        return new UserGameCollection(
            $this->userGameService->fetchAllGamesForUser($user, $params, new UserGameEntity())
        );
    }

    /**
     * Fetch a game for a user
     *
     * The authenticated user must be allowed to fetch a game
     *
     * @SWG\Get(path="/user/{user_id}/game/{game_id}",
     *   tags={"user", "game"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="User Id of the user",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="game_id",
     *     in="path",
     *     description="Game Id to be fetched",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Game was fetched",
     *     @SWG\Schema(ref="#/definitions/UserGameEntity")
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Game not found",
     *     @SWG\Schema(ref="#/definitions/NotFoundError")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to access game",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  string $gameId
     *
     * @return GameEntity
     */
    public function fetch($gameId)
    {
        $userId = $this->getEvent()->getRouteParam('user_id');

        // 404 if the user is not found
        $user = $this->userService->fetchUser($userId);

        // 404 if the game is not found
        $game = $this->gameService->fetchGame($gameId);

        // 404 if the user is not allowed to access the game
        return $this->userGameService->fetchGameForUser($user, $game, new UserGameEntity());
    }
}
