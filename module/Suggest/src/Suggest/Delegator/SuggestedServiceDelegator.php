<?php

namespace Suggest\Delegator;

use Application\Utils\ServiceTrait;
use Suggest\Service\SuggestedService;
use Suggest\Service\SuggestedServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerInterface;

/**
 * Class SuggestedServiceDelegator
 */
class SuggestedServiceDelegator implements SuggestedServiceInterface
{
    use ServiceTrait;

    /**
     * @var SuggestedService
     */
    protected $realService;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * SuggestedServiceDelegator constructor.
     * @param SuggestedService $realService
     * @param EventManagerInterface $events
     */
    public function __construct(SuggestedService $realService, EventManagerInterface $events)
    {
        $this->realService = $realService;
        $this->events      = $events;
        $events->addIdentifiers(array_merge(
            [SuggestedServiceInterface::class, static::class, SuggestedService::class],
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
    public function fetchSuggestedFriendForUser($user, $suggestion, $prototype = null)
    {
        $eventParams = ['user' => $user, 'suggestion' => $suggestion, 'prototype' => $prototype];
        $event       = new Event('fetch.suggested.friend', $this->realService, $eventParams);
        $response    = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->realService->fetchSuggestedFriendForUser($user, $suggestion, $prototype);
            $event->setParam('result', $return);
            $event->setName('fetch.suggested.friend.post');
        } catch (\Exception $exception) {
            $eventParams['exception'] = $exception;
            $event->setName('fetch.suggested.friend.error');
            $this->getEventManager()->triggerEvent($event);
            throw $exception;
        }

        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     * @todo Catch and throw error events
     */
    public function fetchSuggestedFriendsForUser($user, $where = null, $prototype = null)
    {
        $where       = $this->createWhere($where);
        $eventParams = ['user' => $user, 'where' => $where, 'prototype' => $prototype];
        $event       = new Event('fetch.suggested.friends', $this->realService, $eventParams);
        $response    = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchSuggestedFriendsForUser($user, $where, $prototype);
        $event->setName('fetch.suggested.friends.post');

        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     * @todo Catch and throw error events
     */
    public function attachSuggestedFriendForUser($user, $suggestion)
    {
        $eventParams = ['user' => $user, 'suggestion' => $suggestion];
        $event       = new Event('attach.suggested.friends', $this->realService, $eventParams);
        $response    = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->attachSuggestedFriendForUser($user, $suggestion);
        $event->setName('attach.suggested.friends.post');

        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     * @todo Catch and throw error events
     */
    public function deleteSuggestionForUser($user, $suggestion)
    {
        $eventParams = ['user' => $user, 'suggestion' => $suggestion];
        $event       = new Event('delete.suggestion', $this->realService, $eventParams);
        $response    = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->deleteSuggestionForUser($user, $suggestion);
        $event->setName('delete.suggestion.post');

        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritDoc
     * @todo Catch and throw error events
     */
    public function deleteAllSuggestionsForUser($user)
    {
        $eventParams = ['user' => $user];
        $event       = new Event('delete.all.suggestions', $this->realService, $eventParams);
        $response    = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->deleteAllSuggestionsForUser($user);
        $event->setName('delete.all.suggestions.post');

        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
