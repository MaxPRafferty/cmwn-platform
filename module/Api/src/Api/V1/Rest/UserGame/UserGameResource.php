<?php

namespace Api\V1\Rest\UserGame;

use Api\V1\Rest\Game\GameCollection;
use Api\V1\Rest\Game\GameEntity;
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
     * @SWG\Post(path="/user/:user_id/game/:game_id",
     *   tags={"user-game"},
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
     *     @SWG\Schema(ref="#/definitions/UserGame")
     *   ),
     *   @SWG\Response(
     *     response=201,
     *     description="Group was created",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/GroupEntity")
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
        return new GameEntity($game->getArrayCopy());
    }

    /**
     * @inheritdoc
     */
    public function delete($id)
    {
        $userId = $this->getEvent()->getRouteParam('user_id');
        $gameId = $this->getEvent()->getRouteParam('game_id');

        $user = $this->userService->fetchUser($userId);
        $game = $this->gameService->fetchGame($gameId);

        return $this->userGameService->detachGameForUser($user, $game);
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($params = [])
    {
        $userId = $this->getEvent()->getRouteParam('user_id');
        $user = $this->userService->fetchUser($userId);

        return new GameCollection($this->userGameService->fetchAllGamesForUser($user, null, new UserGameEntity()));
    }

    /**
     * @inheritdoc
     */
    public function fetch($id)
    {
        $userId = $this->getEvent()->getRouteParam('user_id');
        $gameId = $this->getEvent()->getRouteParam('game_id');

        $user = $this->userService->fetchUser($userId);
        $game = $this->gameService->fetchGame($gameId);

        $game = $this->userGameService->fetchGameForUser($user, $game, new UserGameEntity());
        return new GameEntity($game->getArrayCopy());
    }
}
