<?php

namespace Feed\Delegator;

use Feed\FeedInterface;
use Feed\Service\FeedService;
use Feed\Service\FeedServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class FeedDelegator
 * @package Feed\Delegator
 */
class FeedDelegator implements FeedServiceInterface
{
    use EventManagerAwareTrait;

    /**
     * @var FeedService
     */
    protected $service;

    /**
     * FeedDelegator constructor.
     * @param FeedService $feedService
     */
    public function __construct(FeedService $feedService)
    {
        $this->service = $feedService;
    }

    /**
     * @inheritdoc
     */
    public function createFeed(FeedInterface $feed)
    {
        $event = new Event(
            'create.feed',
            $this->service,
            ['feed' => $feed]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->service->createFeed($feed);

        $event->setName('create.feed.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchFeed($feedId, $where = null, $prototype = null)
    {
        $event = new Event(
            'fetch.feed',
            $this->service,
            ['feed_id' => $feedId, 'where' => $where, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->service->fetchFeed($feedId, $where, $prototype);

            $event->setName('fetch.feed.post');
            $event->setParam('feed', $return);

            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $e) {
            $event->setName('fetch.feed.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, $prototype = null)
    {
        $event = new Event(
            'fetch.all.feed',
            $this->service,
            ['where' => $where, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->service->fetchAll($where, $prototype);

        $event->setName('fetch.all.feed.post');
        $event->setParam('feeds', $return);

        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function updateFeed(FeedInterface $feed)
    {
        $event = new Event(
            'update.feed',
            $this->service,
            ['feed' => $feed]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->service->updateFeed($feed);

        $event->setName('update.feed.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function deleteFeed(FeedInterface $feed, $soft = true)
    {
        $event = new Event(
            'delete.feed',
            $this->service,
            ['feed' => $feed, 'soft' => $soft]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->service->deleteFeed($feed, $soft);

        $event->setName('delete.feed.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
