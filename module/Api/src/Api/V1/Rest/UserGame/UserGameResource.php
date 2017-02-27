<?php

namespace Api\V1\Rest\UserGame;

use Game\Service\GameServiceInterface;
use Game\Service\UserGameService;
use Game\Service\UserGameServiceInterface;
use User\Service\UserServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class UserGameResource
 * @package Api\V1\Rest\UserGame
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

    /**
     * UserGameResource constructor.
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
        $this->userService = $userService;
        $this->gameService = $gameService;
    }

    /**
     * Attach a game to a user
     *
     * The authenticated user must be allowed to attach a game to a user in the system
     *
     * @SWG\Post(path="/user/{user_id}/game/{game_id}",
     *   tags={"user-game"},
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
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/UserGameEntity")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to attach game",
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
        $userId = $this->getEvent()->getRouteParam('user_id');
        $gameId = $this->getEvent()->getRouteParam('game_id');

        $user = $this->userService->fetchUser($userId);
        $game = $this->gameService->fetchGame($gameId);

        $this->userGameService->attachGameToUser($user, $game);
        return new UserGameEntity($game->getArrayCopy());
    }

    /**
     * Detach a game from a user
     *
     * A fetch is done first to ensure the user has access to a game. The authenticated user will get a 403
     * if the they are not allowed to detach a game from a user
     *
     * @SWG\Delete(path="/user/{user_id}/game/{game_id}",
     *   tags={"user-game"},
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
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/NotFoundError")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to detach or access game",
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
     * @param  string $id
     *
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
        $userId = $this->getEvent()->getRouteParam('user_id');
        $gameId = $this->getEvent()->getRouteParam('game_id');

        $user = $this->userService->fetchUser($userId);
        $game = $this->gameService->fetchGame($gameId);

        if ($this->userGameService->detachGameForUser($user, $game)) {
            return true;
        }

        return new ApiProblem(500, 'failed to detach game from user');
    }

    /**
     * Fetches multiple games the requested user has access too
     *
     * A User can request all games or all the child games descending from parent.  Empty results are returned if the
     * user is not allowed access to a parent
     *
     * @SWG\Get(path="/user/{user_id}/game",
     *   tags={"user-game"},
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
     *     @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref="#/definitions/UserGameCollection")
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
        $userId = $this->getEvent()->getRouteParam('user_id');
        $user = $this->userService->fetchUser($userId);

        return new UserGameCollection($this->userGameService->fetchAllGamesForUser($user, null, new UserGameEntity()));
    }

    /**
     * Fetch a game for a user
     *
     * The authenticated user must be allowed to fetch a game
     *
     * @SWG\Get(path="/user/{user_id}/game/{game_id}",
     *   tags={"user-game"},
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
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/UserGameEntity")
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
     *     response=403,
     *     description="Not Authorized to access game",
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
     * @param  mixed $id
     *
     * @return ApiProblem|mixed
     */
    public function fetch($id)
    {
        $userId = $this->getEvent()->getRouteParam('user_id');
        $gameId = $this->getEvent()->getRouteParam('game_id');

        $user = $this->userService->fetchUser($userId);
        $game = $this->gameService->fetchGame($gameId);

        $game = $this->userGameService->fetchGameForUser($user, $game, new UserGameEntity());
        return new UserGameEntity($game->getArrayCopy());
    }
}
