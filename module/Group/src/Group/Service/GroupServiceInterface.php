<?php

namespace Group\Service;

use Application\Exception\NotFoundException;
use Group\GroupInterface;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Interface that defines a group service
 */
interface GroupServiceInterface
{
    /**
     * @param GroupInterface $parent
     * @param GroupInterface $child
     *
     * @return bool
     */
    public function attachChildToGroup(GroupInterface $parent, GroupInterface $child): bool;

    /**
     * Returns all the child groups from a group
     *
     * @param GroupInterface $group
     * @param null|PredicateInterface|array $where
     * @param null|GroupInterface $prototype
     *
     * @return AdapterInterface
     */
    public function fetchChildGroups(
        GroupInterface $group,
        $where = null,
        GroupInterface $prototype = null
    ): AdapterInterface;

    /**
     * Saves a new group
     *
     * @param GroupInterface $group
     *
     * @return bool
     * @throws NotFoundException
     */
    public function createGroup(GroupInterface $group): bool;

    /**
     * Updates a group
     *
     * @param GroupInterface $group
     *
     * @return bool
     * @throws NotFoundException
     */
    public function updateGroup(GroupInterface $group): bool;

    /**
     * Fetches one group from the DB using the id
     *
     * @param string $groupId
     * @param GroupInterface|null $prototype
     *
     * @return GroupInterface
     */
    public function fetchGroup(string $groupId, GroupInterface $prototype = null): GroupInterface;

    /**
     * Fetches on group from the DB by using the external id
     *
     * @param string $networkId
     * @param string $externalId
     * @param GroupInterface $prototype
     *
     * @return GroupInterface
     */
    public function fetchGroupByExternalId(
        string $networkId,
        string $externalId,
        GroupInterface $prototype = null
    ): GroupInterface;

    /**
     * Fetches all groups based on parameters
     *
     * @param null $where
     * @param GroupInterface|null $prototype
     *
     * @return AdapterInterface
     */
    public function fetchAll($where = null, GroupInterface $prototype = null): AdapterInterface;

    /**
     * Deletes a group from the database
     *
     * Soft deletes unless soft is false
     *
     * @param GroupInterface $group
     * @param bool $soft
     *
     * @return bool
     */
    public function deleteGroup(GroupInterface $group, bool $soft = true): bool;

    /**
     * Fetches all the types of groups for the children
     *
     * Used for hal link building
     *
     * @param GroupInterface $group
     *
     * @return string[]
     * @deprecated
     */
    public function fetchChildTypes(GroupInterface $group): array;

    /**
     * Fetches all the types of groups
     *
     * @return string[]
     * @deprecated
     */
    public function fetchGroupTypes(): array;
}
