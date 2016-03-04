<?php

namespace Group\Service;

use Application\Exception\NotFoundException;
use Group\GroupInterface;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Interface GroupServiceInterface
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
interface GroupServiceInterface
{
    /**
     * Attaches a user to a group
     *
     * @param GroupInterface $group
     * @param UserInterface $user
     * @param $role
     * @return bool
     */
    public function attachUserToGroup(GroupInterface $group, UserInterface $user, $role);

    /**
     * Detaches a user from a group
     *
     * @param GroupInterface $group
     * @param UserInterface $user
     * @return bool
     */
    public function detachUserFromGroup(GroupInterface $group, UserInterface $user);

    /**
     * @param GroupInterface $parent
     * @param GroupInterface $child
     * @return bool
     */
    public function addChildToGroup(GroupInterface $parent, GroupInterface $child);

    /**
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null);

    /**
     * Saves a group
     *
     * If the group id is null, then a new group is created
     *
     * @param GroupInterface $group
     * @return bool
     * @throws NotFoundException
     */
    public function saveGroup(GroupInterface $group);

    /**
     * Fetches one group from the DB using the id
     *
     * @param $groupId
     * @return GroupInterface
     * @throws NotFoundException
     */
    public function fetchGroup($groupId);

    /**
     * Deletes a group from the database
     *
     * Soft deletes unless soft is false
     *
     * @param GroupInterface $group
     * @param bool $soft
     * @return bool
     */
    public function deleteGroup(GroupInterface $group, $soft = true);
}
