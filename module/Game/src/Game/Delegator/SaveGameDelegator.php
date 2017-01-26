<?php

namespace Game\Delegator;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Game\SaveGame;
use Game\SaveGameInterface;
use Game\Service\SaveGameService;
use Game\Service\SaveGameServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;

/**
 * Class SaveGameDelegator
 */
class SaveGameDelegator implements SaveGameServiceInterface
{
    use ServiceTrait;

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
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->events;
    }

    /**
     * Saves a game
     *
     * @param SaveGameInterface $gameData
     *
     * @return bool
     */
    public function saveGame(SaveGameInterface $gameData)
    {
        $event    = new Event('save.user.game', $this->realService, ['game_data' => $gameData]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->saveGame($gameData);
        $event->setName('save.user.game.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * Deletes a save for a user
     *
     * @param $user
     * @param $game
     *
     * @return bool
     */
    public function deleteSaveForUser($user, $game)
    {
        $event    = new Event('delete.user.save.game', $this->realService, ['user' => $user, 'game' => $game]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->deleteSaveForUser($user, $game);
        $event->setName('delete.user.save.game.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * Fetches a save for a user
     *
     * @param $user
     * @param $game
     * @param null $prototype
     * @param null $where
     *
     * @return SaveGame|SaveGameInterface
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function fetchSaveGameForUser($user, $game, $prototype = null, $where = null)
    {
        $where    = $this->createWhere($where);
        $event    = new Event(
            'fetch.user.save.game',
            $this->realService,
            [
                'user'      => $user,
                'game'      => $game,
                'prototype' => $prototype,
                'where'     => $where
            ]
        );
        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }
        $return = $this->realService->fetchSaveGameForUser($user, $game, $prototype, $where);
        $event->setParam('game_data', $return);
        $event->setName('fetch.user.save.game.post');
        $this->getEventManager()->triggerEvent($event);
        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchAllSaveGamesForUser($user, $where = null, $prototype = null)
    {
        $event = new Event(
            'fetch.user.saves',
            $this->realService,
            [
                'user'      => $user,
                'prototype' => $prototype,
                'where'     => $where
            ]
        );
        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchAllSaveGamesForUser($user, $where, $prototype);
        $event->setParam('user-saves', $return);
        $event->setName('fetch.user.saves.post');
        $this->getEventManager()->triggerEvent($event);
        return $return;
    }


    /**
     * @inheritdoc
     */
    public function fetchAllSaveGameData($where = null, $prototype = null)
    {
        $event = new Event(
            'fetch.game-data',
            $this->realService,
            [
                'where' => $where,
                'prototype' => $prototype
            ]
        );

        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchAllSaveGameData($where, $prototype);

        $event->setParam('game-data', $return);
        $event->setName('fetch.game-data.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
