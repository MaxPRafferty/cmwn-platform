<?php

namespace Api\V1\Rest\SaveGame;

use Game\GameInterface;
use Game\Service\SaveGameServiceInterface;
use User\UserInterface;
use Zend\Json\Json;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class SaveGameResource
 */
class SaveGameResource extends AbstractResourceListener
{
    /**
     * @var SaveGameServiceInterface
     */
    protected $saveService;

    /**
     * SaveGameResource constructor.
     *
     * @param SaveGameServiceInterface $saveGameService
     */
    public function __construct(SaveGameServiceInterface $saveGameService)
    {
        $this->saveService = $saveGameService;
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|SaveGameEntity
     */
    public function create($data)
    {
        /** @var UserInterface $user */
        $user = $this->getEvent()->getRouteParam('user');
        /** @var GameInterface $game */
        $game = $this->getEvent()->getRouteParam('game');

        $saveGame = new SaveGameEntity();
        $saveGame->setData($this->getInputFilter()->getValue('data'));
        $saveGame->setVersion($this->getInputFilter()->getValue('version'));
        $saveGame->setUserId($user->getUserId());
        $saveGame->setGameId($game->getGameId());

        $this->saveService->saveGame($saveGame);
        return $saveGame;
    }

    /**
     * Delete a resource
     *
     * @param  mixed $gameId
     * @return ApiProblem|mixed
     */
    public function delete($gameId)
    {
        /** @var UserInterface $user */
        $user = $this->getEvent()->getRouteParam('user');
        $this->saveService->deleteSaveForUser($user, $gameId);
        return true;
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $gameId
     * @return ApiProblem|mixed
     */
    public function fetch($gameId)
    {
        /** @var UserInterface $user */
        $user   = $this->getEvent()->getRouteParam('user');
        $entity = new SaveGameEntity();

        $this->saveService->fetchSaveGameForUser($user, $gameId, $entity);
        return $entity;
    }

    /**
     * @param array $params
     * @return mixed|ApiProblem
     */
    public function fetchAll($params = [])
    {
        /** @var UserInterface $user */
        $user   = $this->getEvent()->getRouteParam('user');
        $saves = $this->saveService->fetchAllSaveGamesForUser($user, null, new SaveGameEntity());
        return new SaveGameCollection($saves);
    }
}
