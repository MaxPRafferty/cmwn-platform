<?php

namespace Flag\Delegator;

use Application\Exception\NotFoundException;
use Flag\FlagInterface;
use Flag\Service\FlagService;
use Flag\Service\FlagServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerInterface;

/**
 * Class FlagDelegator
 * @package Flag\Delegator
 */
class FlagDelegator implements FlagServiceInterface
{
    use EventManagerAwareTrait;

    /**
     * @var FlagService
     */
    protected $realService;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * FlagDelegator constructor.
     * @param $realService
     * @param EventManagerInterface $events
     */
    public function __construct($realService, EventManagerInterface $events)
    {
        $this->realService = $realService;
        $this->events = $events;
        $events->addIdentifiers(array_merge(
            [FlagServiceInterface::class, static::class, FlagService::class],
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
    public function fetchAll($where = null, $prototype = null)
    {
        $event = new Event(
            'fetch.all.flagged.images',
            $this->realService,
            [ 'where' => $where, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return   = $this->realService->fetchAll($where, $prototype);
        $event->setName('fetch.all.flagged.images.post');
        $event->setParam('flagged-images', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function saveFlag(FlagInterface $flag)
    {
        $event    = new Event('save.flagged.image', $this->realService, ['flag-data' => $flag]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->saveFlag($flag);
        $event->setName('save.flagged.image.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchFlag($flagId, $prototype = null)
    {
        $event = new Event(
            'fetch.flagged.image',
            $this->realService,
            [ 'flag_id' => $flagId, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->realService->fetchFlag($flagId, $prototype);
            $event->setName('fetch.flagged.image.post');
            $event->setParam('flagged-image', $return);
            $this->getEventManager()->triggerEvent($event);
        } catch (NotFoundException $nf) {
            $event->setName('fetch.flagged.image.error');
            $event->setParam('exception', $nf->getMessage());
            $this->getEventManager()->triggerEvent($event);
            throw $nf;
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function updateFlag(FlagInterface $flag)
    {
        $event    = new Event('update.flagged.image', $this->realService, ['flag-data' => $flag]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->updateFlag($flag);
        $event->setName('update.flagged.image.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function deleteFlag(FlagInterface $flag)
    {
        $event    = new Event('delete.flagged.image', $this->realService, ['flag-data' => $flag]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->deleteFlag($flag);
        $event->setName('delete.flagged.image.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
