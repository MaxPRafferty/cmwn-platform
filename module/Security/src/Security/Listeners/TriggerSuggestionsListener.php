<?php

namespace Security\Listeners;

use Suggest\Engine\SuggestionEngine;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class TriggerSuggestionsListener
 * @package Security\Listeners
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
     * TriggerSuggestionsListener constructor.
     * @param SuggestionEngine $suggestionEngine
     */
    public function __construct($suggestionEngine)
    {
        $this->suggestionEngine = $suggestionEngine;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            UserServiceInterface::class,
            'save.new.user.post',
            [$this,'triggerSuggestions']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        $manager->detach(UserServiceInterface::class, $this->listeners[0]);
    }

    /**
     * @param Event $event
     */
    public function triggerSuggestions(Event $event)
    {
        $user = $event->getParam('user');
        if (!$user instanceof UserInterface) {
            return;
        }

        if (!$this->suggestionEngine instanceof SuggestionEngine) {
            return;
        }

        $this->suggestionEngine->exchangeArray(['user_id' => $user->getUserId()]);

        $this->suggestionEngine->perform();
    }
}
