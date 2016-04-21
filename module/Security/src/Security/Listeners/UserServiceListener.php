<?php

namespace Security\Listeners;

use Application\Exception\NotAuthorizedException;
use Group\Service\UserGroupServiceInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Security\Exception\ChangePasswordException;
use Security\SecurityUser;
use User\Service\UserServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class UserServiceListener
 */
class UserServiceListener implements RbacAwareInterface, AuthenticationServiceAwareInterface
{
    use RbacAwareTrait;
    use AuthenticationServiceAwareTrait;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var UserGroupServiceInterface
     */
    protected $userGroupService;

    /**
     * UserServiceListener constructor.
     * @param UserGroupServiceInterface $userGroupService
     */
    public function __construct(UserGroupServiceInterface $userGroupService)
    {
        $this->userGroupService = $userGroupService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            UserServiceInterface::class,
            'fetch.all.users',
            [$this, 'fetchAll']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $listener) {
            $manager->detach(UserServiceInterface::class, $listener);
        }
    }

    /**
     * @param Event $event
     * @return void|\Zend\Paginator\Adapter\DbSelect
     * @throws NotAuthorizedException
     */
    public function fetchAll(Event $event)
    {
        if (!$this->getAuthenticationService()->hasIdentity()) {
            throw new NotAuthorizedException;
        }

        /** @var SecurityUser $user */
        try {
            $user = $this->getAuthenticationService()->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $user = $changePassword->getUser();
        }

        if ($this->getRbac()->isGranted($user->getRole(), 'view.all.users')) {
            return;
        }

        $event->stopPropagation(true);
        return $this->userGroupService->fetchAllUsersForUser(
            $user,
            $event->getParam('where'),
            $event->getParam('prototype')
        );
    }
}
