<?php

namespace Group\Service;

use Application\Exception\NotFoundException;
use Group\GroupInterface;
use Org\OrganizationInterface;
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
    public function createGroup(GroupInterface $group);

    /**
     * Fetches one group from the DB using the id
     *
     * @param $groupId
     * @return GroupInterface
     * @throws NotFoundException
     */
    public function fetchGroup($groupId);

    /**
     * Fetches on group from the DB by using the external id
     *
     * @param string $networkId
     * @param $externalId
     *
     * @return GroupInterface
     */
    public function fetchGroupByExternalId($networkId, $externalId);

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

    /**
     * Fetches all the types of groups for the children
     *
     * Used for hal link building
     *
     * @param GroupInterface $group
     * @return string[]
     */
    public function fetchChildTypes(GroupInterface $group);

    /**
     * Fetches all the children groups for a given group
     *
     * @param GroupInterface $group
     * @param null|PredicateInterface|array $where
     * @param null|object $prototype
     * @return DbSelect
     */
    public function fetchChildGroups(GroupInterface $group, $where = null, $prototype = null);

    /**
     * Fetches all the types of groups
     *
     * @return string[]
     */
    public function fetchGroupTypes();

    /**
     * Updates a group
     *
     *
     * @param GroupInterface $group
     *
     * @return bool
     * @throws NotFoundException
     */
    public function updateGroup(GroupInterface $group);
}
