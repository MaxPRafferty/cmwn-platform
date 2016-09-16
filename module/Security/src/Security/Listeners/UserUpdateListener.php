<?php

namespace Security\Listeners;

use Application\Exception\NotAuthorizedException;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\SecurityUser;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

class UserUpdateListener implements AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            UserServiceInterface::class,
            'save.user.post',
            [$this,'checkUserName']
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
     * Checks if the user logged in is the one updating the profile
     *
     * @param Event $event
     * @throws NotAuthorizedException
     */
    public function checkUserName(Event $event)
    {
        if (!$this->getAuthenticationService()->hasIdentity()) {
            throw new NotAuthorizedException;
        }

        /** @var SecurityUser $loggedIn */
        try {
            $loggedIn = $this->getAuthenticationService()->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            return;
        }

        $user = $event->getParam('user');

        if (!$user instanceof UserInterface) {
            return;
        }

        if ($loggedIn->getUserId() === $user->getUserId()) {
            $event->getTarget()->updateUserName($user, $user->getUserName());
        };
    }
}
