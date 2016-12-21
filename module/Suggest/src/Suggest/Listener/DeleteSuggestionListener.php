<?php

namespace Suggest\Listener;

use Friend\Service\FriendServiceInterface;
use Suggest\Service\SuggestedServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Removes a suggestion if the user friends a user
 *
 * @package Suggest\Listener
 */
class DeleteSuggestionListener
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var SuggestedServiceInterface
     */
    protected $suggestedService;

    /**
     * DeleteSuggestionListener constructor.
     *
     * @param SuggestedServiceInterface $suggestedService
     */
    public function __construct(SuggestedServiceInterface $suggestedService)
    {
        $this->suggestedService = $suggestedService;
    }

    /**
     * @param SharedEventManagerInterface $events
     * @codeCoverageIgnore
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            FriendServiceInterface::class,
            'attach.friend.post',
            [$this, 'deleteSuggestionIfFriend']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     * @codeCoverageIgnore
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        $manager->detach(FriendServiceInterface::class, $this->listeners[0]);
    }

    /**
     * @param Event $event
     */
    public function deleteSuggestionIfFriend(Event $event)
    {
        $user   = $event->getParam('user');
        $friend = $event->getParam('friend');

        $this->suggestedService->deleteSuggestionForUser($user, $friend);
    }
}
