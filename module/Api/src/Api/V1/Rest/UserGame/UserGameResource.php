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
     * @inheritdoc
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

        $this->userGameService->detachGameForUser($user, $game);
        return new ApiProblem(200, 'game detached from user');
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($params = [])
    {
        $userId = $this->getEvent()->getRouteParam('user_id');
        $user = $this->userService->fetchUser($userId);

        return new GameCollection($this->userGameService->fetchAllGamesForUser($user, null, new GameEntity()));
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

        $game = $this->userGameService->fetchGameForUser($user, $game, new GameEntity());
        return new GameEntity($game->getArrayCopy());
    }
}
