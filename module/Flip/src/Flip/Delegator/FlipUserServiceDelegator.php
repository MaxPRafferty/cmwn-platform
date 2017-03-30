<?php

namespace Flip\Delegator;

use Flip\EarnedFlipInterface;
use Flip\Service\FlipUserService;
use Flip\Service\FlipUserServiceInterface;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * A Flip User Service that triggers events
 */
class FlipUserServiceDelegator implements FlipUserServiceInterface
{
    /**
     * @var FlipUserService
     */
    protected $realService;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * FlipUserDelegator constructor.
     *
     * @param FlipUserService $flipService
     * @param EventManagerInterface $events
     */
    public function __construct(FlipUserService $flipService, EventManagerInterface $events)
    {
        $this->realService = $flipService;
        $this->events      = $events;
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->events;
    }

    /**
     * @param $where
     *
     * @return \Zend\Db\Sql\Predicate\PredicateInterface|\Zend\Db\Sql\Predicate\PredicateSet|\Zend\Db\Sql\Where
     */
    public function createWhere($where)
    {
        return $this->realService->createWhere($where);
    }

    /**
     * @inheritdoc
     */
    public function fetchEarnedFlipsForUser(
        string $userId,
        $where = null,
        EarnedFlipInterface $prototype = null
    ): AdapterInterface {
        $where = $this->createWhere($where);
        $event = new Event(
            'fetch.user.flips',
            $this->realService,
            ['where' => $where, 'prototype' => $prototype, 'user' => $userId]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchEarnedFlipsForUser($userId, $where, $prototype);
        } catch (\Throwable $fetchEarnedException) {
            $event->setName('fetch.user.flips.error');
            $event->setParam('error', $fetchEarnedException);
            $this->getEventManager()->triggerEvent($event);

            throw $fetchEarnedException;
        }

        $event->setName('fetch.user.flips.post');
        $event->setParam('flips', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function attachFlipToUser(UserInterface $user, string $flipId): bool
    {
        $event = new Event(
            'attach.flip',
            $this->realService,
            ['user' => $user, 'flip' => $flipId]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->attachFlipToUser($user, $flipId);
        } catch (\Throwable $attachException) {
            $event->setName('attach.flip.error');
            $event->setParam('error', $attachException);
            $this->getEventManager()->triggerEvent($event);
            throw $attachException;
        }

        $event->setName('attach.flip.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritDoc
     */
    public function acknowledgeFlip(EarnedFlipInterface $earnedFlip): bool
    {
        $event = new Event(
            'acknowledge.flip',
            $this->realService,
            ['earned_flip' => $earnedFlip]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->acknowledgeFlip($earnedFlip);
        } catch (\Throwable $attachException) {
            $event->setName('acknowledge.flip.error');
            $event->setParam('error', $attachException);
            $this->getEventManager()->triggerEvent($event);
            throw $attachException;
        }

        $event->setName('acknowledge.flip.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritDoc
     */
    public function fetchFlipsForUser(
        UserInterface $user,
        string $flipId,
        EarnedFlipInterface $prototype = null
    ): AdapterInterface {
        $event = new Event(
            'fetch.earned.user.flips',
            $this->realService,
            ['flip_id' => $flipId, 'prototype' => $prototype, 'user' => $user]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchFlipsForUser($user, $flipId, $prototype);
        } catch (\Throwable $fetchEarnedException) {
            $event->setName('fetch.earned.user.flips.error');
            $event->setParam('error', $fetchEarnedException);
            $this->getEventManager()->triggerEvent($event);

            throw $fetchEarnedException;
        }

        $event->setName('fetch.earned.user.flips.post');
        $event->setParam('flips', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritDoc
     */
    public function fetchLatestAcknowledgeFlip(
        UserInterface $user,
        EarnedFlipInterface $prototype = null
    ): EarnedFlipInterface {
        $event = new Event(
            'fetch.acknowledge.flip',
            $this->realService,
            ['prototype' => $prototype, 'user' => $user]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchLatestAcknowledgeFlip($user, $prototype);
        } catch (\Throwable $fetchAcknowledgeException) {
            $event->setName('fetch.acknowledge.flip.error');
            $event->setParam('error', $fetchAcknowledgeException);
            $this->getEventManager()->triggerEvent($event);

            throw $fetchAcknowledgeException;
        }

        $event->setName('fetch.acknowledge.flip.post');
        $event->setParam('flip', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
