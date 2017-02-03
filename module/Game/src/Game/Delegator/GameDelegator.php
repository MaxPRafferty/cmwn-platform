<?php

namespace Game\Delegator;

use Application\Utils\HideDeletedEntitiesListener;
use Application\Utils\ServiceTrait;
use Game\GameInterface;
use Game\Service\GameService;
use Game\Service\GameServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;

/**
 * Class GameDelegator
 * @package Game\Delegator
 */
class GameDelegator implements GameServiceInterface
{
    use ServiceTrait;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * @var GameServiceInterface
     */
    protected $gameService;

    /**
     * @inheritdoc
     */
    public function __construct(GameServiceInterface $gameService, EventManagerInterface $events)
    {
        $this->gameService = $gameService;

        $this->events = $events;
        $deleted = new HideDeletedEntitiesListener(
            ['fetch.all.games'],
            ['fetch.game.post']
        );

        $deleted->attach($events, PHP_INT_MIN);
        $deleted->setEntityParamKey('game');
        $events->addIdentifiers(array_merge(
            [GameServiceInterface::class, static::class, GameService::class],
            $events->getIdentifiers()
        ));
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->events;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, $prototype = null, bool $deleted = false)
    {
        $where = $this->createWhere($where);
        $event = new Event(
            'fetch.all.games',
            $this->gameService,
            ['where' => $where, 'prototype' => $prototype, 'show_deleted' => $deleted]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->gameService->fetchAll($where, $prototype, $event->getParam('show_deleted'));
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
