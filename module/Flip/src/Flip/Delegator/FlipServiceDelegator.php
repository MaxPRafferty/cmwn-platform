<?php

namespace Flip\Delegator;

use Flip\FlipInterface;
use Flip\Service\FlipService;
use Flip\Service\FlipServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Flip Delegator that dispatches events
 */
class FlipServiceDelegator implements FlipServiceInterface
{
    /**
     * @var FlipService
     */
    protected $realService;

    /**
     * @var EventManagerInterface $events
     */
    protected $events;

    /**
     * FlipServiceDelegator constructor.
     *
     * @param FlipService $flipService
     * @param EventManagerInterface $events
     */
    public function __construct(FlipService $flipService, EventManagerInterface $events)
    {
        $this->realService = $flipService;
        $this->events = $events;
        $events->addIdentifiers(array_merge(
            [FlipServiceInterface::class, static::class, FlipService::class],
            $events->getIdentifiers()
        ));
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
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->events;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, FlipInterface $prototype = null): AdapterInterface
    {
        $where = $this->createWhere($where);
        $event = new Event(
            'fetch.all.flips',
            $this->realService,
            ['where' => $where, 'prototype' => $prototype]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchAll($where, $prototype);
        } catch (\Throwable $fetchAllException) {
            $event->setName('fetch.all.flips.error');
            $event->setParam('error', $fetchAllException);
            $this->getEventManager()->triggerEvent($event);

            throw $fetchAllException;
        }

        $event->setName('fetch.all.flips.post');
        $event->setParam('flips', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchFlipById($flipId, FlipInterface $prototype = null): FlipInterface
    {
        $event = new Event('fetch.flip', $this->realService, ['flip_id' => $flipId]);
        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchFlipById($flipId);
        } catch (\Throwable $fetchFlipException) {
            $event->setName('fetch.flip.error');
            $event->setParam('error', $fetchFlipException);
            $this->getEventManager()->triggerEvent($event);

            throw $fetchFlipException;
        }

        $event->setParam('flip', $return);
        $event->setName('fetch.flip.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function createFlip(FlipInterface $flip): bool
    {
        $event = new Event('create.flip', $this->realService, ['flip' => $flip]);
        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->createFlip($flip);
        } catch (\Throwable $createFlipException) {
            $event->setName('create.flip.error');
            $event->setParam('error', $createFlipException);
            $this->getEventManager()->triggerEvent($event);

            throw $createFlipException;
        }

        $event->setParam('return', $return);
        $event->setName('create.flip.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function updateFlip(FlipInterface $flip): bool
    {
        $event = new Event('update.flip', $this->realService, ['flip' => $flip]);
        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->updateFlip($flip);
        } catch (\Throwable $updateFlipException) {
            $event->setName('update.flip.error');
            $event->setParam('error', $updateFlipException);
            $this->getEventManager()->triggerEvent($event);

            throw $updateFlipException;
        }

        $event->setParam('return', $return);
        $event->setName('update.flip.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function deleteFlip(FlipInterface $flip): bool
    {
        $event = new Event('delete.flip', $this->realService, ['flip' => $flip]);
        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->deleteFlip($flip);
        } catch (\Throwable $deleteFlipException) {
            $event->setName('delete.flip.error');
            $event->setParam('error', $deleteFlipException);
            $this->getEventManager()->triggerEvent($event);

            throw $deleteFlipException;
        }

        $event->setParam('return', $return);
        $event->setName('delete.flip.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
