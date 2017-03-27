<?php

namespace Group\Delegator;

use Application\Utils\HideDeletedEntitiesListener;
use Application\Utils\ServiceTrait;
use Group\GroupInterface;
use Group\Service\UserGroupService;
use Group\Service\UserGroupServiceInterface;
use Org\OrganizationInterface;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * A Delegator for the UserGroupService that dispatches events before calling the service
 */
class UserGroupServiceDelegator implements UserGroupServiceInterface
{
    use ServiceTrait;

    /**
     * @var UserGroupService
     */
    protected $realService;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * UserGroupServiceDelegator constructor.
     *
     * @param UserGroupService $realService
     * @param EventManagerInterface $events
     */
    public function __construct(UserGroupService $realService, EventManagerInterface $events)
    {
        $this->realService = $realService;
        $this->events      = $events;
        $this->events->addIdentifiers(array_merge(
            [UserGroupServiceInterface::class, static::class, UserGroupService::class],
            $events->getIdentifiers()
        ));

        $hideListener = new HideDeletedEntitiesListener(
            ['fetch.group.users', 'fetch.org.users', 'fetch.all.user.users'],
            []
        );

        $hideListener->setEntityParamKey('item');
        $hideListener->setDeletedField('u.deleted');
        $hideListener->attach($this->events);
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager(): EventManagerInterface
    {
        return $this->events;
    }

    /**
     * @inheritdoc
     */
    public function getAlias(): string
    {
        return $this->realService->getAlias();
    }

    /**
     * @inheritdoc
     */
    public function attachUserToGroup(GroupInterface $group, UserInterface $user, $role): bool
    {
        $eventParams = ['group' => $group, 'user' => $user, 'role' => $role];
        $event       = new Event('attach.user', $this->realService, $eventParams);
        if ($this->getEventManager()->triggerEvent($event)->stopped()) {
            return false;
        }

        try {
            $return = $this->realService->attachUserToGroup($group, $user, $role);
        } catch (\Exception $attachException) {
            $eventParams['exception'] = $attachException;
            $event                    = new Event('attach.user.error', $this->realService, $eventParams);
            $this->getEventManager()->triggerEvent($event);

            return false;
        }

        $event = new Event('attach.user.post', $this->realService, $eventParams);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function detachUserFromGroup(GroupInterface $group, UserInterface $user): bool
    {
        $eventParams = ['group' => $group, 'user' => $user];
        $event       = new Event('detach.user', $this->realService, $eventParams);
        if ($this->getEventManager()->triggerEvent($event)->stopped()) {
            return false;
        }

        try {
            $return = $this->realService->detachUserFromGroup($group, $user);
        } catch (\Exception $attachException) {
            $eventParams['exception'] = $attachException;
            $event                    = new Event('detach.user.error', $this->realService, $eventParams);
            $this->getEventManager()->triggerEvent($event);

            return false;
        }

        $event = new Event('detach.user.post', $this->realService, $eventParams);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchUsersForGroup(
        GroupInterface $group,
        $where = null,
        UserInterface $prototype = null
    ): AdapterInterface {
        $where       = $this->createWhere($where);
        $eventParams = ['group' => $group, 'where' => $where];
        $event       = new Event('fetch.group.users', $this->realService, $eventParams);
        $response    = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->realService->fetchUsersForGroup($group, $where, $prototype);
        } catch (\Exception $attachException) {
            $event->setParam('exception', $attachException);
            $event->setName('fetch.group.users.error');
            $this->getEventManager()->triggerEvent($event);
            throw $attachException;
        }

        $event->setName('fetch.group.users.post');
        $event->setParam('results', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchUsersForOrg(
        OrganizationInterface $organization,
        $where = null,
        UserInterface $prototype = null
    ): AdapterInterface {
        $where       = $this->createWhere($where);
        $eventParams = ['organization' => $organization, 'where' => $where];
        $event       = new Event('fetch.org.users', $this->realService, $eventParams);
        $response    = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->realService->fetchUsersForOrg($organization, $where, $prototype);
        } catch (\Exception $exception) {
            $event->setParam('exception', $exception);
            $event->setName('fetch.org.users.error');
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('fetch.org.users.post');
        $event->setParam('result', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchGroupsForUser(
        UserInterface $user,
        $where = null,
        GroupInterface $prototype = null
    ): AdapterInterface {
        $eventParams = ['user' => $user];
        $event       = new Event('fetch.user.group', $this->realService, $eventParams);
        $response    = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->realService->fetchGroupsForUser($user, $where, $prototype);
        } catch (\Exception $fetchException) {
            $event->setParam('exception', $fetchException);
            $event->setName('fetch.user.group.error');
            $this->getEventManager()->triggerEvent($event);

            throw $fetchException;
        }

        $event->setName('fetch.user.group.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchOrganizationsForUser(
        UserInterface $user,
        OrganizationInterface $prototype = null
    ): AdapterInterface {
        $eventParams = ['user' => $user];
        $event       = new Event('fetch.user.orgs', $this->realService, $eventParams);
        $response    = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->realService->fetchOrganizationsForUser($user, $prototype);
        } catch (\Exception $attachException) {
            $event->setParam('exception', $attachException);
            $event->setName('fetch.user.orgs.error');
            $this->getEventManager()->triggerEvent($event);

            throw $attachException;
        }

        $event->setName('fetch.user.orgs.post');
        $event->setParam('result', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchAllUsersForUser(
        UserInterface $user,
        $where = null,
        UserInterface $prototype = null
    ): AdapterInterface {
        $eventParams = ['user' => $user, 'where' => $where, 'prototype' => $prototype];
        $event       = new Event('fetch.all.user.users', $this->realService, $eventParams);
        $response    = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->realService->fetchAllUsersForUser($user, $where, $prototype);
            $event->setParam('result', $return);
            $event->setName('fetch.all.user.users.post');
        } catch (\Exception $attachException) {
            $event->setParam('exception', $attachException);
            $event->setName('fetch.all.user.users.error');
            $this->getEventManager()->triggerEvent($event);

            throw $attachException;
        }

        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
