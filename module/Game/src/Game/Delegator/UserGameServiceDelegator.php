<?php

namespace Game\Delegator;

use Application\Utils\HideDeletedEntitiesListener;
use Application\Utils\ServiceTrait;
use Game\GameInterface;
use Game\Service\UserGameService;
use Game\Service\UserGameServiceInterface;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Class UserGameServiceDelegator
 */
class UserGameServiceDelegator implements UserGameServiceInterface
{
    use ServiceTrait;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * @var UserGameServiceInterface
     */
    protected $service;

    /**
     * UserGameServiceDelegator constructor.
     * @param UserGameServiceInterface $userGameService
     * @param EventManagerInterface $events
     */
    public function __construct(UserGameServiceInterface $userGameService, EventManagerInterface $events)
    {
        $this->service = $userGameService;
        $this->events = $events;

        $deleted = new HideDeletedEntitiesListener(
            ['fetch.all.user.games'],
            ['fetch.user.game.post'],
            'g'
        );

        $deleted->attach($events, PHP_INT_MIN);
        $deleted->setEntityParamKey('game');

        $events->addIdentifiers(array_merge(
            [UserGameServiceInterface::class, static::class, UserGameService::class],
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
    public function fetchAllGamesForUser(
        UserInterface $user,
        $where = null,
        GameInterface $prototype = null
    ) : AdapterInterface {
        $where = $this->createWhere($where);
        $event = new Event(
            'fetch.all.user.games',
            $this->service,
            ['user' => $user, 'where' => $where, 'prototype' => $prototype]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->service->fetchAllGamesForUser($user, $where, $prototype);
        } catch (\Exception $e) {
            $event->setName('fetch.all.user.games.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }

        $event->setName('fetch.all.user.games.post');
        $event->setParam('games', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchGameForUser(
        UserInterface $user,
        GameInterface $game,
        GameInterface $prototype = null
    ) : GameInterface {
        $event = new Event(
            'fetch.user.game',
            $this->service,
            [ 'user' => $user, 'game' => $game ]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }
        try {
            $return = $this->service->fetchGameForUser($user, $game, $prototype);
        } catch (\Exception $e) {
            $event->setName('fetch.user.game.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }

        $event->setName('fetch.user.game.post');
        $event->setParam('game', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function attachGameToUser(UserInterface $user, GameInterface $game) : bool
    {
        $event = new Event(
            'attach.user.game',
            $this->service,
            [ 'user' => $user, 'game' => $game ]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }
        try {
            $return = $this->service->attachGameToUser($user, $game);
        } catch (\Exception $e) {
            $event->setName('attach.user.game.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }

        $event->setName('attach.user.game.post');
        $event->setParam('return', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function detachGameForUser(UserInterface $user, GameInterface $game) : bool
    {
        $event = new Event(
            'detach.user.game',
            $this->service,
            [ 'user' => $user, 'game' => $game ]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }
        try {
            $return = $this->service->detachGameForUser($user, $game);
        } catch (\Exception $e) {
            $event->setName('detach.user.game.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }

        $event->setName('detach.user.game.post');
        $event->setParam('return', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
