<?php

namespace Group\Delegator;

use Application\Utils\HideDeletedEntitiesListener;
use Group\GroupInterface;
use Group\Service\UserGroupService;
use Group\Service\UserGroupServiceInterface;
use User\UserInterface;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Class UserGroupServiceDelegator
 * @package Group\Delegator
 */
class UserGroupServiceDelegator implements UserGroupServiceInterface, EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    /**
     * @var UserGroupService
     */
    protected $realService;

    /**
     * @var array
     */
    protected $eventIdentifier = [
        UserGroupServiceInterface::class,
        UserGroupService::class
    ];

    /**
     * UserGroupServiceDelegator constructor.
     * @param UserGroupService $realService
     */
    public function __construct(UserGroupService $realService)
    {
        $this->realService     = $realService;
    }

    /**
     * Attaches the HideDeleteEntitiesListener
     */
    protected function attachDefaultListeners()
    {
        $hideListener = new HideDeletedEntitiesListener(
            ['fetch.group.users', 'fetch.org.users', 'fetch.all.user.users'],
            []
        );

        $hideListener->setEntityParamKey('item');
        $hideListener->setDeletedField('u.deleted');

        $this->getEventManager()->attach($hideListener);
    }

    /**
     * Attaches a user to a group
     *
     * @param GroupInterface $group
     * @param UserInterface $user
     * @param RoleInterface|string $role
     * @return bool
     * @throws \RuntimeException
     */
    public function attachUserToGroup(GroupInterface $group, UserInterface $user, $role)
    {
        $eventParams = ['group' => $group, 'user' => $user, 'role' => $role];
        $event       = new Event('attach.user', $this->realService, $eventParams);
        if ($this->getEventManager()->trigger($event)->stopped()) {
            return false;
        }

        try {
            $return = $this->realService->attachUserToGroup($group, $user, $role);
        } catch (\Exception $attachException) {
            $eventParams['exception'] = $attachException;
            $event                    = new Event('attach.user.error', $this->realService, $eventParams);
            $this->getEventManager()->trigger($event);

            return false;
        }

        $event = new Event('attach.user.post', $this->realService, $eventParams);
        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * Detaches a user from a group
     *
     * @param GroupInterface $group
     * @param UserInterface $user
     * @return bool
     */
    public function detachUserFromGroup(GroupInterface $group, UserInterface $user)
    {
        $eventParams = ['group' => $group, 'user' => $user];
        $event       = new Event('detach.user', $this->realService, $eventParams);
        if ($this->getEventManager()->trigger($event)->stopped()) {
            return false;
        }

        try {
            $return = $this->realService->detachUserFromGroup($group, $user);
        } catch (\Exception $attachException) {
            $eventParams['exception'] = $attachException;
            $event                    = new Event('detach.user.error', $this->realService, $eventParams);
            $this->getEventManager()->trigger($event);

            return false;
        }

        $event = new Event('detach.user.post', $this->realService, $eventParams);
        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * @param GroupInterface|\Zend\Db\Sql\Where $group
     * @param null $prototype
     * @return bool
     */
    public function fetchUsersForGroup(GroupInterface $group, $prototype = null)
    {
        $eventParams = ['group' => $group];
        $event       = new Event('fetch.group.users', $this->realService, $eventParams);
        if ($this->getEventManager()->trigger($event)->stopped()) {
            return false;
        }

        try {
            $return = $this->realService->fetchUsersForGroup($group, $prototype);
        } catch (\Exception $attachException) {
            $eventParams['exception'] = $attachException;
            $event                    = new Event('fetch.group.users.error', $this->realService, $eventParams);
            $this->getEventManager()->trigger($event);

            return false;
        }

        $event = new Event('fetch.group.users.post', $this->realService, $eventParams);
        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * @param $organization
     * @param null $prototype
     * @return bool
     */
    public function fetchUsersForOrg($organization, $prototype = null)
    {
        $eventParams = ['organization' => $organization];
        $event       = new Event('fetch.org.users', $this->realService, $eventParams);
        if ($this->getEventManager()->trigger($event)->stopped()) {
            return false;
        }

        try {
            $return = $this->realService->fetchUsersForOrg($organization, $prototype);
        } catch (\Exception $attachException) {
            $eventParams['exception'] = $attachException;
            $event                    = new Event('fetch.org.users.error', $this->realService, $eventParams);
            $this->getEventManager()->trigger($event);

            return false;
        }

        $event = new Event('fetch.org.users.post', $this->realService, $eventParams);
        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * Finds all the groups for a user
     *
     * SELECT *
     * FROM groups g
     * LEFT JOIN user_groups ug ON ug.group_id = g.group_id
     * WHERE ug.user_id = 'baz-bat'
     *
     * @param Where|GroupInterface|string $user
     * @param object $prototype
     * @return DbSelect
     */
    public function fetchGroupsForUser($user, $prototype = null)
    {
        $eventParams = ['user' => $user];
        $event       = new Event('fetch.user.group', $this->realService, $eventParams);
        if ($this->getEventManager()->trigger($event)->stopped()) {
            return false;
        }

        try {
            $return = $this->realService->fetchGroupsForUser($user, $prototype);
        } catch (\Exception $attachException) {
            $eventParams['exception'] = $attachException;
            $event->setName('fetch.user.group.error');
            $this->getEventManager()->trigger($event);

            return false;
        }

        $event->setName('fetch.user.group.post');
        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * Fetches organizations for a user
     *
     * @param Where|GroupInterface|string $user
     * @param mixed $prototype
     * @return DbSelect
     */
    public function fetchOrganizationsForUser($user, $prototype = null)
    {
        $eventParams = ['user' => $user];
        $event       = new Event('fetch.user.orgs', $this->realService, $eventParams);
        if ($this->getEventManager()->trigger($event)->stopped()) {
            return false;
        }

        try {
            $return = $this->realService->fetchOrganizationsForUser($user, $prototype);
        } catch (\Exception $attachException) {
            $eventParams['exception'] = $attachException;
            $event->setName('fetch.user.orgs.error');
            $this->getEventManager()->trigger($event);

            return false;
        }

        $event->setName('fetch.user.orgs.post');
        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * Fetches all the Users a user has a relationship with
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     * @return bool|DbSelect
     */
    public function fetchAllUsersForUser($user, $where = null, $prototype = null)
    {
        $eventParams = ['user' => $user, 'where' => $where, 'prototype' => $prototype];
        $event       = new Event('fetch.all.user.users', $this->realService, $eventParams);
        $response    = $this->getEventManager()->trigger($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->realService->fetchAllUsersForUser($user, $where, $prototype);
            $event->setParam('result', $return);
            $event->setName('fetch.all.user.users');
        } catch (\Exception $attachException) {
            $eventParams['exception'] = $attachException;
            $event->setName('fetch.all.user.users.error');
            $this->getEventManager()->trigger($event);
            return false;
        }

        $this->getEventManager()->trigger($event);
        return $return;
    }
}
