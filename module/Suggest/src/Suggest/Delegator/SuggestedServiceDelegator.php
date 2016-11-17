<?php

namespace Suggest\Delegator;

use Application\Utils\ServiceTrait;
use Suggest\Service\SuggestedService;
use Suggest\Service\SuggestedServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class SuggestedServiceDelegator
 */
class SuggestedServiceDelegator implements SuggestedServiceInterface
{
    use EventManagerAwareTrait;
    use ServiceTrait;

    /**
     * @var SuggestedService
     */
    protected $realService;

    /**
     * SuggestedServiceDelegator constructor.
     * @param SuggestedService $realService
     */
    public function __construct(SuggestedService $realService)
    {
        $this->realService = $realService;
    }

    /**
     * @inheritdoc
     */
    public function fetchSuggestedFriendForUser($user, $suggestion, $prototype = null)
    {
        $eventParams = ['user' => $user, 'suggestion' => $suggestion, 'prototype' => $prototype];
        $event       = new Event('fetch.suggested.friend', $this->realService, $eventParams);
        $response    = $this->getEventManager()->trigger($event);
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
            $this->getEventManager()->trigger($event);
            throw $exception;
        }

        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * Fetches the suggested users for a user
     * @inheritdoc
     */
    public function fetchSuggestedFriendsForUser($user, $where = null, $prototype = null)
    {
        $where       = $this->createWhere($where);
        $eventParams = ['user' => $user, 'where' => $where, 'prototype' => $prototype];
        $event       = new Event('fetch.suggested.friends', $this->realService, $eventParams);
        $response    = $this->getEventManager()->trigger($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchSuggestedFriendsForUser($user, $where, $prototype);
        $event->setName('fetch.suggested.friends.post');

        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * @inheritdoc
     */
    public function attachSuggestedFriendForUser($user, $suggestion)
    {
        $eventParams = ['user' => $user, 'suggestion' => $suggestion];
        $event       = new Event('attach.suggested.friends', $this->realService, $eventParams);
        $response    = $this->getEventManager()->trigger($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->attachSuggestedFriendForUser($user, $suggestion);
        $event->setName('attach.suggested.friends.post');

        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * @inheritdoc
     */
    public function deleteSuggestionForUser($user, $suggestion)
    {
        $eventParams = ['user' => $user, 'suggestion' => $suggestion];
        $event = new Event('delete.suggestion', $this->realService, $eventParams);
        $response = $this->getEventManager()->trigger($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->deleteSuggestionForUser($user, $suggestion);
        $event->setName('delete.suggestion.post');

        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * @inheritDoc
     */
    public function deleteAllSuggestionsForUser($user)
    {
        $eventParams = ['user' => $user];
        $event = new Event('delete.all.suggestion', $this->realService, $eventParams);
        $response = $this->getEventManager()->trigger($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->deleteAllSuggestionsForUser($user);
        $event->setName('delete.all.suggestion.post');

        $this->getEventManager()->trigger($event);
        return $return;
    }

}
