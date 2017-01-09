<?php

namespace Flip\Delegator;

use Application\Utils\ServiceTrait;
use Flip\FlipInterface;
use Flip\Service\FlipService;
use Flip\Service\FlipServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Flip Delegator that dispatches events
 */
class FlipServiceDelegator implements FlipServiceInterface
{
    use EventManagerAwareTrait;
    use ServiceTrait;

    /**
     * @var FlipService
     */
    protected $realService;

    /**
     * FlipServiceDelegator constructor.
     *
     * @param FlipService $flipService
     * @param EventManagerInterface $eventManager
     */
    public function __construct(FlipService $flipService, EventManagerInterface $eventManager)
    {
        $this->realService = $flipService;
        $this->setEventManager($eventManager);
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
}
