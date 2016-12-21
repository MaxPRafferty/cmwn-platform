<?php

namespace Game\Delegator;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Game\GameInterface;
use Game\Service\GameServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class GameDelegator
 * @package Game\Delegator
 */
class GameDelegator implements GameServiceInterface
{
    use EventManagerAwareTrait;
    use ServiceTrait;

    /**
     * @var GameServiceInterface
     */
    protected $gameService;

    /**
     * GameDelegator constructor.
     * @param GameServiceInterface $gameService
     */
    public function __construct(GameServiceInterface $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where = $this->createWhere($where);
        $event = new Event(
            'fetch.all.games',
            $this->gameService,
            ['where' => $where, 'paginate' => $paginate, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->gameService->fetchAll($where, $paginate, $prototype);
        $event->setName('fetch.all.games.post');

        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchGame($gameId)
    {
        $event    = new Event('fetch.game', $this->gameService, ['game_id' => $gameId]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->gameService->fetchGame($gameId);
        $event->setName('fetch.game.post');
        $event->setParam('game', $return);

        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function saveGame(GameInterface $game)
    {
        $event = new Event('update.game', $this->gameService, ['game' => $game]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->gameService->saveGame($game);
        $event->setName('update.game.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function createGame(GameInterface $game)
    {
        $event = new Event('create.game', $this->gameService, ['game' => $game]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->gameService->createGame($game);
        $event->setName('create.game.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function deleteGame(GameInterface $game, $soft = true)
    {
        $event = new Event('delete.game', $this->gameService, ['game' => $game]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->gameService->deleteGame($game);
        $event->setName('delete.game.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
