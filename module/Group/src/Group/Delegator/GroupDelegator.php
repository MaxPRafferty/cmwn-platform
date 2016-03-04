<?php

namespace Group\Delegator;

use Application\Exception\NotFoundException;
use Application\Utils\HideDeletedEntitiesListener;
use Group\Service\GroupService;
use Group\Service\GroupServiceInterface;
use Group\GroupInterface;
use User\User;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class GroupServiceDelegator
 * @package Group\Delegator
 */
class GroupDelegator implements GroupServiceInterface
{
    use EventManagerAwareTrait;

    /**
     * @var GroupService
     */
    protected $realService;

    public function __construct(GroupService $service)
    {
        $this->realService = $service;
    }

    protected function attachDefaultListeners()
    {
        $hideListener = new HideDeletedEntitiesListener(['fetch.all.groups'], ['fetch.group.post']);
        $hideListener->setEntityParamKey('group');

        $this->getEventManager()->attach($hideListener);
    }

    /**
     * Attaches a user to a group
     *
     * @param GroupInterface $group
     * @param UserInterface $user
     * @param $role
     * @return bool
     */
    public function attachUserToGroup(GroupInterface $group, UserInterface $user, $role)
    {
        // TODO: Implement attachUserToGroup() method.
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
        // TODO: Implement detachUserFromGroup() method.
    }


    /**
     * @param GroupInterface $parent
     * @param GroupInterface $child
     * @return bool
     */
    public function addChildToGroup(GroupInterface $parent, GroupInterface $child)
    {
        // TODO: Implement addChildToGroup() method.
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
        $where    = !$where instanceof PredicateInterface ? new Where($where) : $where;
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
}
