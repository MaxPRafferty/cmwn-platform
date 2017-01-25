<?php

namespace Feed\Delegator;

use Feed\Service\FeedUserService;
use Feed\Service\FeedUserServiceInterface;
use Feed\UserFeedInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;

/**
 * Class FeedUserDelegator
 * @package Feed\Delegator\
 */
class FeedUserDelegator implements FeedUserServiceInterface
{
    /**
     * @var FeedUserService
     */
    protected $service;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * FeedUserDelegator constructor.
     * @param FeedUserService $service
     * @param EventManagerInterface $events
     */
    public function __construct(FeedUserService $service, EventManagerInterface $events)
    {
        $this->service = $service;
        $this->events = $events;
        $events->addIdentifiers(array_merge(
            [FeedUserServiceInterface::class, static::class, FeedUserService::class],
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
    public function attachFeedForUser($user, UserFeedInterface $feed)
    {
        $event = new Event(
            'attach.user.feed',
            $this->service,
            ['user' => $user, 'user_feed' => $feed]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->service->attachFeedForUser($user, $feed);

            $event->setName('attach.user.feed.post');
            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $e) {
            $event->setName('attach.user.feed.error');
            $this->getEventManager()->triggerEvent($event);
            $event->setParam('exception', $e);

            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function fetchFeedForUser($user, string $feedId, $where = null, UserFeedInterface $prototype = null)
    {
        $event = new Event(
            'fetch.user.feed',
            $this->service,
            ['user' => $user, 'feed_id' => $feedId, 'where' => $where, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->service->fetchFeedForUser($user, $feedId, $where, $prototype);

            $event->setName('fetch.user.feed.post');
            $event->setParam('user_feed', $return);

            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $e) {
            $event->setName('fetch.user.feed.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function fetchAllFeedForUser($user, $where = null, UserFeedInterface $prototype = null)
    {
        $event = new Event(
            'fetch.all.user.feed',
            $this->service,
            ['user' => $user, 'where' => $where, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->service->fetchAllFeedForUser($user, $where, $prototype);

            $event->setName('fetch.all.user.feed.post');
            $event->setParam('user_feeds', $return);

            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $e) {
            $event->setName('fetch.all.user.feed.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);

            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function updateFeedForUser($user, UserFeedInterface $feed)
    {
        $event = new Event(
            'update.user.feed',
            $this->service,
            ['user' => $user, 'user_feed' => $feed]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->service->updateFeedForUser($user, $feed);

            $event->setName('update.user.feed.post');
            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $e) {
            $event->setName('update.user.feed.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);

            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function deleteFeedForUser($user, UserFeedInterface $feed)
    {
        $event = new Event(
            'delete.user.feed',
            $this->service,
            ['user' => $user, 'user_feed' => $feed]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->service->deleteFeedForUser($user, $feed);

            $event->setName('delete.user.feed.post');
            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $e) {
            $event->setName('delete.user.feed.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);

            throw $e;
        }
    }
}
