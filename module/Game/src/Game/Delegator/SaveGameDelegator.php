<?php

namespace Game\Delegator;

use Game\SaveGameInterface;
use Game\Service\SaveGameService;
use Game\Service\SaveGameServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Delegates control to the SaveGameService
 */
class SaveGameDelegator implements SaveGameServiceInterface
{
    /**
     * @var SaveGameService
     */
    protected $realService;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * SaveGameDelegator constructor.
     *
     * @param SaveGameService $realService
     * @param EventManagerInterface $events
     */
    public function __construct(SaveGameService $realService, EventManagerInterface $events)
    {
        $this->realService = $realService;
        $this->events      = $events;
        $events->addIdentifiers(array_merge(
            [SaveGameServiceInterface::class, static::class, SaveGameService::class],
            $events->getIdentifiers()
        ));
    }

    /**
     * Proxies to the real service to create a single where
     *
     * @param null $where
     *
     * @return \Zend\Db\Sql\Predicate\PredicateInterface|\Zend\Db\Sql\Predicate\PredicateSet|\Zend\Db\Sql\Where
     */
    public function createWhere($where = null)
    {
        return $this->realService->createWhere($where);
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager(): EventManagerInterface
    {
        return $this->events;
    }

    /**
     * @inheritdoc
     */
    public function saveGame(SaveGameInterface $gameData): bool
    {
        $event = new Event('save.user.game', $this->realService, ['game_data' => $gameData]);
        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }
            $return = $this->realService->saveGame($gameData);
        } catch (\Exception $exception) {
            $event->setName('save.user.game.error');
            $event->setParam('error', $exception);
            $this->getEventManager()->triggerEvent($event);
            throw $exception;
        }

        $event->setName('save.user.game.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function deleteSaveForUser($user, $game): bool
    {
        $event = new Event('delete.user.save.game', $this->realService, ['user' => $user, 'game' => $game]);
        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->deleteSaveForUser($user, $game);
        } catch (\Exception $exception) {
            $event->setName('delete.user.game.error');
            $event->setParam('error', $exception);
            $this->getEventManager()->triggerEvent($event);
            throw $exception;
        }

        $event->setName('delete.user.save.game.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchSaveGameForUser(
        $user,
        $game,
        SaveGameInterface $prototype = null,
        $where = null
    ): SaveGameInterface {
        $where = $this->createWhere($where);
        $event = new Event(
            'fetch.user.save.game',
            $this->realService,
            [
                'user'      => $user,
                'game'      => $game,
                'prototype' => $prototype,
                'where'     => $where,
            ]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }
            $return = $this->realService->fetchSaveGameForUser($user, $game, $prototype, $where);
        } catch (\Exception $exception) {
            $event->setName('fetch.user.save.game.error');
            $event->setParam('error', $exception);
            $this->getEventManager()->triggerEvent($event);
            throw $exception;
        }

        $event->setParam('game_data', $return);
        $event->setName('fetch.user.save.game.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchAllSaveGamesForUser(
        $user,
        $where = null,
        SaveGameInterface $prototype = null
    ): AdapterInterface {
        $where = $this->createWhere($where);
        $event = new Event(
            'fetch.user.saves',
            $this->realService,
            [
                'user'      => $user,
                'prototype' => $prototype,
                'where'     => $where,
            ]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchAllSaveGamesForUser($user, $where, $prototype);
        } catch (\Exception $exception) {
            $event->setName('fetch.user.saves.error');
            $event->setParam('error', $exception);
            $this->getEventManager()->triggerEvent($event);
            throw $exception;
        }

        $event->setParam('user-saves', $return);
        $event->setName('fetch.user.saves.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchAllSaveGameData(
        $where = null,
        SaveGameInterface $prototype = null
    ): AdapterInterface {
        $where = $this->createWhere($where);
        $event = new Event(
            'fetch.game-data',
            $this->realService,
            [
                'where'     => $where,
                'prototype' => $prototype,
            ]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchAllSaveGameData($where, $prototype);
        } catch (\Exception $exception) {
            $event->setName('fetch.game-data.error');
            $event->setParam('error', $exception);
            $this->getEventManager()->triggerEvent($event);
            throw $exception;
        }

        $event->setParam('game-data', $return);
        $event->setName('fetch.game-data.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
