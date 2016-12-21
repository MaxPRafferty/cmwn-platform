<?php

namespace Security\Listeners;

use Application\Exception\NotAuthorizedException;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Exception\ChangePasswordException;
use Security\SecurityUser;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;
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

        $this->listeners[] = $events->attach(
            UserServiceInterface::class,
            'save.user',
            [$this,'checkEmail']
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
     * @throws NotAuthorizedException
     */
    public function checkEmail(Event $event)
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

        if ($loggedIn->isSuper() || $user->getUserId() === $loggedIn->getUserId()) {
            return;
        }

        /**@var UserServiceInterface*/
        $userService = $event->getTarget();

        if (!$userService instanceof UserServiceInterface) {
            return;
        }

        $existing = $userService->fetchUser($user->getUserId());

        $user->setEmail($existing->getEmail());
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

        if ($loggedIn->getUserId() === $user->getUserId() || $loggedIn->isSuper()) {
            $event->getTarget()->updateUserName($user, $user->getUserName());
        };
    }
}
