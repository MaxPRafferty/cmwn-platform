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
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Where;
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
     *
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

        $this->listeners[] = $events->attach(
            UserServiceInterface::class,
            'fetch.all.users',
            [$this, 'removeActiveUser']
        );

        $this->listeners[] = $events->attach(
            UserGroupServiceInterface::class,
            'fetch.org.users',
            [$this, 'removeActiveUser']
        );

        $this->listeners[] = $events->attach(
            UserGroupServiceInterface::class,
            'fetch.group.users',
            [$this, 'removeActiveUser']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        $manager->detach(UserServiceInterface::class, $this->listeners[0]);
        $manager->detach(UserServiceInterface::class, $this->listeners[1]);
        $manager->detach(UserGroupServiceInterface::class, $this->listeners[2]);
        $manager->detach(UserGroupServiceInterface::class, $this->listeners[3]);
    }

    /**
     * Removes the id of the current logged in user from the query
     *
     * @param Event $event
     */
    public function removeActiveUser(Event $event)
    {
        if (!$this->getAuthenticationService()->hasIdentity()) {
            return;
        }

        /** @var SecurityUser $user */
        try {
            $user = $this->getAuthenticationService()->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            return;
        }

        /** @var Where $where */
        $where = $event->getParam('where');
        $where->addPredicate(new Operator('u.user_id', '!=', $user->getUserId()));
    }

    /**
     * @param Event $event
     *
     * @return null|\Zend\Paginator\Adapter\DbSelect
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
            return null;
        }

        $event->stopPropagation(true);

        return $this->userGroupService->fetchAllUsersForUser(
            $user,
            $event->getParam('where'),
            $event->getParam('prototype')
        );
    }
}
