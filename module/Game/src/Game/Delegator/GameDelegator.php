<?php

namespace Game\Delegator;

use Application\Utils\HideDeletedEntitiesListener;
use Game\GameInterface;
use Game\Service\GameService;
use Game\Service\GameServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Calls the game service with event calls
 */
class GameDelegator implements GameServiceInterface
{
    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * @var GameService
     */
    protected $gameService;

    /**
     * GameDelegator constructor.
     *
     * @param GameService $gameService
     * @param EventManagerInterface $events
     */
    public function __construct(GameService $gameService, EventManagerInterface $events)
    {
        $this->gameService = $gameService;
        $this->events      = $events;
        $deleted           = new HideDeletedEntitiesListener(['fetch.all.games'], ['fetch.game.post']);

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
     * Calls the service where to ensure that fields are aliased
     *
     * @param $where
     *
     * @return \Zend\Db\Sql\Predicate\PredicateInterface|\Zend\Db\Sql\Predicate\PredicateSet|\Zend\Db\Sql\Where
     */
    public function createWhere($where)
    {
        return $this->gameService->createWhere($where);
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, GameInterface $prototype = null, bool $deleted = false): AdapterInterface
    {
        $where = $this->createWhere($where);
        $event = new Event(
            'fetch.all.games',
            $this->gameService,
            ['where' => $where, 'prototype' => $prototype, 'show_deleted' => $deleted]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $results = $this->gameService->fetchAll($where, $prototype, $event->getParam('show_deleted'));
        } catch (\Exception $gameException) {
            $event->setName('fetch.all.games.error');
            $event->setParam('error', $gameException);
            $this->getEventManager()->triggerEvent($event);
            throw $gameException;
        }

        $event->setName('fetch.all.games.post');
        $event->setParam('results', $results);
        $this->getEventManager()->triggerEvent($event);

        return $results;
    }

    /**
     * @inheritdoc
     */
    public function fetchGame(string $gameId, GameInterface $prototype = null): GameInterface
    {
        $event = new Event('fetch.game', $this->gameService, ['game_id' => $gameId, 'prototype' => $prototype]);

        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->gameService->fetchGame($gameId);
        } catch (\Exception $gameException) {
            $event->setName('fetch.game.error');
            $event->setParam('error', $gameException);
            $this->getEventManager()->triggerEvent($event);
            throw $gameException;
        }

        $event->setName('fetch.game.post');
        $event->setParam('game', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function saveGame(GameInterface $game): bool
    {
        $event = new Event('update.game', $this->gameService, ['game' => $game]);

        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->gameService->saveGame($game);
        } catch (\Exception $gameException) {
            $event->setName('update.game.error');
            $event->setParam('error', $gameException);
            $this->getEventManager()->triggerEvent($event);
            throw $gameException;
        }

        $event->setName('update.game.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function createGame(GameInterface $game): bool
    {
        $event = new Event('create.game', $this->gameService, ['game' => $game]);

        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->gameService->createGame($game);
        } catch (\Exception $gameException) {
            $event->setName('create.game.error');
            $event->setParam('error', $gameException);
            $this->getEventManager()->triggerEvent($event);
            throw $gameException;
        }
        $event->setName('create.game.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function deleteGame(GameInterface $game, bool $soft = true): bool
    {
        $event = new Event('delete.game', $this->gameService, ['game' => $game, 'soft' => $soft]);
        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->gameService->deleteGame($game);
        } catch (\Exception $gameException) {
            $event->setName('delete.game.error');
            $event->setParam('error', $gameException);
            $this->getEventManager()->triggerEvent($event);
            throw $gameException;
        }

        $event->setName('delete.game.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
