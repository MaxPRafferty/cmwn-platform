<?php

namespace Security\Listeners;

use Application\Exception\NotAuthorizedException;
use Asset\Service\UserImageServiceInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class FetchUserImageListener
 */
class FetchUserImageListener implements AuthenticationServiceAwareInterface
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
            UserImageServiceInterface::class,
            'fetch.user.image',
            [$this, 'checkUser']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        $manager->detach(UserImageServiceInterface::class, $this->listeners[0]);
    }

    /**
     * @param Event $event
     *
     * @throws NotAuthorizedException
     */
    public function checkUser(Event $event)
    {
        if (!$this->getAuthenticationService()->hasIdentity()) {
            return;
        }

        $loggedIn     = $this->getAuthenticationService()->getIdentity();
        $user         = $event->getParam('user');
        $userId       = $user instanceof UserInterface ? $user->getUserId() : $user;
        $approvedOnly = ($userId !== $loggedIn->getUserId());

        $event->setParam('approved_only', $approvedOnly);
    }
}
