<?php

namespace Group\Delegator;

use Application\Utils\HideDeletedEntitiesListener;
use Application\Utils\ServiceTrait;
use Group\Service\GroupService;
use Group\Service\GroupServiceInterface;
use Group\GroupInterface;
use Zend\EventManager\Event;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * A Delegator for the group service that dispatches events before calling the Group Service
 */
class GroupDelegator implements GroupServiceInterface
{
    use ServiceTrait;

    /**
     * @var GroupServiceInterface
     */
    protected $realService;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * GroupDelegator constructor.
     *
     * @param GroupService $service
     * @param EventManagerInterface $events
     */
    public function __construct(
        GroupService $service,
        EventManagerInterface $events
    ) {
        $this->realService = $service;
        $this->events      = $events;
        $this->events->addIdentifiers(array_merge(
            [GroupServiceInterface::class, static::class, GroupService::class],
            $events->getIdentifiers()
        ));

        $hideListener = new HideDeletedEntitiesListener(
            ['fetch.all.groups', 'fetch.user.groups'],
            ['fetch.group.post']
        );

        $hideListener->setEntityParamKey('group');
        $hideListener->setDeletedField('g.deleted');
        $hideListener->attach($events);
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
    public function fetchChildGroups(
        GroupInterface $group,
        $where = null,
        GroupInterface $prototype = null
    ): AdapterInterface {
        $where = $this->createWhere($where);
        $event = new Event(
            'fetch.child.groups',
            $this->realService,
            ['group' => $group, 'where' => $where, 'prototype' => $prototype]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchChildGroups($group, $where, $prototype);
        } catch (\Throwable $exception) {
            $event->setName('fetch.child.groups.error');
            $event->setParam('exception', $exception);
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('fetch.child.groups.post');
        $event->setParam('result', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function createGroup(GroupInterface $group): bool
    {
        $event    = new Event('save.group', $this->realService, ['group' => $group]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->realService->createGroup($group);
            $event->setName('save.group.post');
            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $groupException) {
            $event->setName('save.group.error');
            $event->setParam('exception', $groupException);
            $this->getEventManager()->triggerEvent($event);
        }

        throw $groupException;
    }

    /**
     * @inheritdoc
     */
    public function updateGroup(GroupInterface $group): bool
    {
        $event    = new Event('update.group', $this->realService, ['group' => $group]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->realService->updateGroup($group);
            $event->setName('update.group.post');
            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $groupException) {
            $event->setName('update.group.error');
            $event->setParam('exception', $groupException);
            $this->getEventManager()->triggerEvent($event);
        }

        throw $groupException;
    }

    /**
     * @inheritdoc
     */
    public function fetchGroup(string $groupId, GroupInterface $prototype = null): GroupInterface
    {
        $event    = new Event('fetch.group', $this->realService, ['group_id' => $groupId, 'prototype' => $prototype]);
        $response = $this->getEventManager()->triggerEvent($event);

        try {
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchGroup($groupId, $prototype);
        } catch (\Throwable $exception) {
            $event->setName('fetch.group.error');
            $event->setParam('exception', $exception);

            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('fetch.group.post');
        $event->setParam('group', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchGroupByExternalId(
        string $networkId,
        string $externalId,
        GroupInterface $prototype = null
    ): GroupInterface {
        $event    = new Event(
            'fetch.group.external',
            $this->realService,
            ['network_id' => $networkId, 'external_id' => $externalId]
        );
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchGroupByExternalId($networkId, $externalId, $prototype);
        $event->setName('fetch.group.external.post');
        $event->setParam('group', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function deleteGroup(GroupInterface $group, bool $soft = true): bool
    {
        $event    = new Event('delete.group', $this->realService, ['group' => $group, 'soft' => $soft]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->deleteGroup($group, $soft);
        $event  = new Event('delete.group.post', $this->realService, ['group' => $group, 'soft' => $soft]);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, GroupInterface $prototype = null): AdapterInterface
    {
        $where = $this->createWhere($where);
        $event = new Event(
            'fetch.all.groups',
            $this->realService,
            ['where' => $where, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchAll($where, $prototype);
        $event  = new Event(
            'fetch.all.groups.post',
            $this->realService,
            ['where' => $where, 'prototype' => $prototype, 'groups' => $return]
        );
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchChildTypes(GroupInterface $group): array
    {
        $event    = new Event('fetch.child.group.types', $this->realService, ['group' => $group]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchChildTypes($group);

        $event->setName('fetch.child.group.types.post');
        $event->setParam('types', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchGroupTypes(): array
    {
        $event    = new Event('fetch.group.types', $this->realService);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchGroupTypes();
        $event->setName('fetch.group.types.post');
        $event->setParam('results', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @param GroupInterface $parent
     * @param GroupInterface $child
     *
     * @return bool
     */
    public function attachChildToGroup(GroupInterface $parent, GroupInterface $child): bool
    {
        $event    = new Event(
            'attach.group.child',
            $this->realService,
            ['parent' => $parent, 'child' => $child]
        );
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->attachChildToGroup($parent, $child);
        $event->setName('fetch.group.types.post');
        $event->setParam('results', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
