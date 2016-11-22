<?php

namespace Suggest\Listener;

use Job\JobInterface;
use Job\Service\JobServiceInterface;
use Suggest\Engine\SuggestionEngine;
use User\Service\UserServiceInterface;
use User\User;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Creates a suggestion job for a user once the user is created
 *
 * @package Suggest\Listeners
 */
class TriggerSuggestionsListener
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var SuggestionEngine
     */
    protected $suggestionEngine;

    /**
     * @var JobServiceInterface
     */
    protected $jobService;

    /**
     * TriggerSuggestionsListener constructor.
     * @param SuggestionEngine $suggestionEngine
     * @param JobServiceInterface $jobService
     */
    public function __construct($suggestionEngine, $jobService)
    {
        $this->suggestionEngine = $suggestionEngine;
        $this->jobService = $jobService;
    }

    /**
     * @param SharedEventManagerInterface $events
     * @codeCoverageIgnore
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            UserServiceInterface::class,
            'save.new.user.post',
            [$this,'triggerSuggestionJob']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     * @codeCoverageIgnore
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        $manager->detach(UserServiceInterface::class, $this->listeners[0]);
    }

    /**
     * @param Event $event
     */
    public function triggerSuggestionJob(Event $event)
    {
        $user = $event->getParam('user');
        if (!$user instanceof UserInterface) {
            return;
        }

        if (!$this->suggestionEngine instanceof JobInterface) {
            return;
        }

        if ($user->getType() !== User::TYPE_CHILD) {
            return;
        }

        $this->suggestionEngine->exchangeArray(['user_id' => $user->getUserId()]);

        $this->jobService->sendJob($this->suggestionEngine);
    }
}
