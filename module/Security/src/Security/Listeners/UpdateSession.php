<?php

namespace Security\Listeners;

use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Stdlib\CallbackHandler;

/**
 * Class UpdateSession
 */
class UpdateSession implements AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;

    /**
     * @var CallbackHandler
     */
    protected $listener;

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listener = $events->attach(
            UserServiceInterface::class,
            'save.user.post',
            [$this, 'updateSession']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        $manager->detach(UserServiceInterface::class, $this->listener);
    }

    /**
     * Updates the saved user in the session when the user is saved
     *
     * @param Event $event
     * @return null
     */
    public function updateSession(Event $event)
    {
        $user = $event->getParam('user');
        if (!$user instanceof UserInterface) {
            return null;
        }

        /** @var UserInterface $securityUser */
        $securityUser = $this->getAuthenticationService()->getIdentity();

        if ($securityUser->getUserId() !== $user->getUserId()) {
            return null;
        }

        $securityUser->exchangeArray($user->getArrayCopy());
    }
}
