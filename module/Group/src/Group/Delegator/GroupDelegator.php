<?php

namespace Group\Delegator;

use Application\Exception\NotFoundException;
use Application\Utils\HideDeletedEntitiesListener;
use Application\Utils\ServiceTrait;
use Group\Service\GroupServiceInterface;
use Group\GroupInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class GroupServiceDelegator
 * @package Group\Delegator
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class GroupDelegator implements GroupServiceInterface
{
    use EventManagerAwareTrait;
    use ServiceTrait;

    /**
     * @var GroupServiceInterface
     */
    protected $realService;

    /**
     * @var string
     */
    protected $eventIdentifier = 'Group\Service\GroupServiceInterface';

    /**
     * GroupDelegator constructor.
     * @param GroupServiceInterface $service
     */
    public function __construct(GroupServiceInterface $service)
    {
        $this->realService = $service;
    }

    /**
     * Attaches the HideDeletedEntitiesListener
     */
    protected function attachDefaultListeners()
    {
        $hideListener = new HideDeletedEntitiesListener(
            ['fetch.all.groups', 'fetch.user.groups'],
            ['fetch.group.post']
        );

        $hideListener->setEntityParamKey('group');
        $hideListener->setDeletedField('g.deleted');

        $this->getEventManager()->attach($hideListener);
    }

    /**
     * @param GroupInterface $parent
     * @param GroupInterface $child
     * @return bool
     * @fixme This is not following the contributing standard
     */
    public function addChildToGroup(GroupInterface $parent, GroupInterface $child)
    {
        $this->realService->addChildToGroup($parent, $child);
    }

    /**
     * Saves a group
     *
     * If the group id is null, then a new group is created
     *
     * @param GroupInterface $group
     * @return bool
     * @throws NotFoundException
     */
    public function saveGroup(GroupInterface $group)
    {
        $event    = new Event('save.group', $this->realService, ['group' => $group]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->saveGroup($group);

        $event    = new Event('save.group.post', $this->realService, ['group' => $group]);
        $this->getEventManager()->trigger($event);

        return $return;

    }

    /**
     * Fetches one group from the DB using the id
     *
     * @param $groupId
     * @return GroupInterface
     * @throws NotFoundException
     */
    public function fetchGroup($groupId)
    {
        $event    = new Event('fetch.group', $this->realService, ['group_id' => $groupId]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchGroup($groupId);
        $event    = new Event('fetch.group.post', $this->realService, ['group_id' => $groupId, 'group' => $return]);
        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * Fetches on group from the DB by using the external id
     *
     * @param \Org\OrganizationInterface|string $organization
     * @param $externalId
     *
     * @return GroupInterface
     */
    public function fetchGroupByExternalId($organization, $externalId)
    {
        $event    = new Event(
            'fetch.group.external',
            $this->realService,
            ['organization' => $organization, 'external_id' => $externalId]
        );
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchGroupByExternalId($organization, $externalId);
        $event->setName('fetch.group.external.post');
        $event->setParam('group', $return);
        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * Deletes a group from the database
     *
     * Soft deletes unless soft is false
     *
     * @param GroupInterface $group
     * @param bool $soft
     * @return bool
     */
    public function deleteGroup(GroupInterface $group, $soft = true)
    {
        $event    = new Event('delete.group', $this->realService, ['group' => $group, 'soft' => $soft]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->deleteGroup($group, $soft);
        $event  = new Event('delete.group.post', $this->realService, ['group' => $group, 'soft' => $soft]);
        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where = $this->createWhere($where);
        $event    = new Event(
            'fetch.all.groups',
            $this->realService,
            ['where' => $where, 'paginate' => $paginate, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->trigger($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return   = $this->realService->fetchAll($where, $paginate, $prototype);
        $event    = new Event(
            'fetch.all.groups.post',
            $this->realService,
            ['where' => $where, 'paginate' => $paginate, 'prototype' => $prototype, 'groups' => $return]
        );
        $this->getEventManager()->trigger($event);

        return $return;
    }

    /**
     * @param GroupInterface|string|Where $user
     * @param null $where
     * @param bool $paginate
     * @param null $prototype
     * @return mixed|HydratingResultSet|DbSelect
     */
    public function fetchAllForUser($user, $where = null, $paginate = true, $prototype = null)
    {
        $where = $this->createWhere($where);
        $event    = new Event(
            'fetch.user.groups',
            $this->realService,
            ['user' => $user, 'where' => $where, 'paginate' => $paginate, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->trigger($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return   = $this->realService->fetchAllForUser($user, $event->getParam('where'), $paginate, $prototype);
        $event->setName('fetch.user.groups.post');
        $event->setParam('result', $return);
        $this->getEventManager()->trigger($event);

        return $return;
    }

    /**
     * Fetches all the types of groups for the children
     *
     * Used for hal link building
     *
     * @param GroupInterface $group
     * @return string[]
     * @deprecated
     */
    public function fetchChildTypes(GroupInterface $group)
    {
        $event    = new Event('fetch.child.group.types', $this->realService, ['group' => $group]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchChildTypes($group);

        $event->setName('fetch.child.group.types.post');
        $event->setParam('types', $return);
        $this->getEventManager()->trigger($event);

        return $return;
    }

    /**
     * Fetches all the children groups for a given group
     *
     * @param GroupInterface $group
     * @param null|PredicateInterface|array $where
     * @param null|object $prototype
     * @return DbSelect
     */
    public function fetchChildGroups(GroupInterface $group, $where = null, $prototype = null)
    {
        $where = $this->createWhere($where);
        $event    = new Event(
            'fetch.child.groups',
            $this->realService,
            ['group' => $group, 'where' => $where, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->trigger($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return   = $this->realService->fetchChildGroups($group, $where, $prototype);
        $event->setName('fetch.child.groups.post');
        $event->setParam('result', $return);
        $this->getEventManager()->trigger($event);

        return $return;
    }

    /**
     * Fetches all the types of groups
     *
     * @return string[]
     */
    public function fetchGroupTypes()
    {
        $event    = new Event('fetch.group.types', $this->realService);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchGroupTypes();
        $event->setName('fetch.group.types.post');
        $event->setParam('results', $return);
        $this->getEventManager()->trigger($event);
        return $return;
    }
}
