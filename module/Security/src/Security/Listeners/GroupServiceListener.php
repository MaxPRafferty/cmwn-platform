<?php

namespace Security\Listeners;

use Application\Exception\NotAuthorizedException;
use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use Group\Service\UserGroupServiceInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Security\Exception\ChangePasswordException;
use Security\SecurityUser;
use Security\Service\SecurityGroupServiceInterface;
use Zend\Db\Sql\Predicate\Operator;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class GroupServiceListener
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GroupServiceListener implements RbacAwareInterface, AuthenticationServiceAwareInterface
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
     * @var SecurityGroupServiceInterface $securityOrgService
     */
    protected $securityGroupService;

    /**
     * GroupServiceListener constructor.
     *
     * @param UserGroupServiceInterface $userGroupService
     * @param SecurityGroupServiceInterface $securityGroupService
     */
    public function __construct(
        UserGroupServiceInterface $userGroupService,
        SecurityGroupServiceInterface $securityGroupService
    ) {
        $this->userGroupService   = $userGroupService;
        $this->securityGroupService = $securityGroupService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            GroupServiceInterface::class,
            'fetch.all.groups',
            [$this, 'fetchAll']
        );

        $this->listeners[] = $events->attach(
            GroupServiceInterface::class,
            'fetch.group.post',
            [$this, 'fetchGroup']
        );

        $this->listeners[] = $events->attach(
            GroupServiceInterface::class,
            'fetch.child.groups',
            [$this, 'fetchChildGroups']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $listener) {
            $manager->detach(GroupServiceInterface::class, $listener);
        }
    }

    /**
     * @param Event $event
     *
     * @return \Zend\Db\ResultSet\HydratingResultSet|\Zend\Paginator\Adapter\DbSelect
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

        if ($this->getRbac()->isGranted($user->getRole(), 'view.all.groups')) {
            return null;
        }

        $event->stopPropagation(true);

        return $this->userGroupService->fetchGroupsForUser(
            $user,
            $event->getParam('where'),
            $event->getParam('prototype')
        );
    }

    /**
     * @param $event
     *
     * @throws NotAuthorizedException
     */
    public function fetchGroup(Event $event)
    {
        $groupId = $event->getParam('group_id', null);
        if (!$this->getAuthenticationService()->hasIdentity()) {
            throw new NotAuthorizedException;
        }

        /** @var SecurityUser $user */
        try {
            $user = $this->getAuthenticationService()->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $user = $changePassword->getUser();
        }

        $user->setRole($this->securityGroupService->getRoleForGroup($groupId, $user));

        if (!$this->getRbac()->isGranted($user->getRole(), 'view.user.groups')) {
            throw new NotAuthorizedException;
        }
    }

    /**
     * @param Event $event
     *
     * @return void|\Zend\Paginator\Adapter\DbSelect
     * @throws NotAuthorizedException
     */
    public function fetchChildGroups(Event $event)
    {
        $groupId = $event->getParam('group_id', null);
        if (!$this->getAuthenticationService()->hasIdentity()) {
            throw new NotAuthorizedException;
        }

        /** @var SecurityUser $user */
        try {
            $user = $this->getAuthenticationService()->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $user = $changePassword->getUser();
        }

        $user->setRole($this->securityGroupService->getRoleForGroup($groupId, $user));

        // User is allowed to view all child groups
        if ($this->getRbac()->isGranted($user->getRole(), 'view.all.child.groups')) {
            return;
        }

        $event->stopPropagation(true);
        /** @var \Zend\Db\Sql\Where $where */
        $group     = $event->getParam('group', false);
        $groupId   = $group instanceof GroupInterface ? $group->getGroupId() : $group;
        $where     = $event->getParam('where');
        $prototype = $event->getParam('prototype');

        $where->addPredicate(new Operator('g.parent_id', '=', $groupId));

        return $this->userGroupService->fetchGroupsForUser($user, $where, $prototype);
    }
}
