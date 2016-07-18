<?php

namespace Group\Service;

use Group\GroupInterface;
use User\UserInterface;
use Zend\Db\Sql\Where;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Interface UserGroupServiceInterface
 * @package Group\Service
 */
interface UserGroupServiceInterface
{

    /**
     * Attaches a user to a group
     *
     * @param GroupInterface $group
     * @param UserInterface $user
     * @param RoleInterface|string $role
     * @return bool
     * @throws \RuntimeException
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
     * Finds all the users for a group
     *
     * @param Where|GroupInterface|string $group
     * @param array $where
     * @param object $prototype
     *
     * @return DbSelect
     */
    public function fetchUsersForGroup(GroupInterface $group, $where = null, $prototype = null);

    /**
     * Finds all the users for an organization
     *
     * @param $organization
     * @param array $where
     * @param null $prototype
     *
     * @return DbSelect
     */
    public function fetchUsersForOrg($organization, $where = null, $prototype = null);

    /**
     * Finds all the users for a group
     *
     * @param Where|GroupInterface|string $user
     * @param null $where
     * @param object $prototype
     *
     * @return DbSelect
     */
    public function fetchGroupsForUser($user, $where = null, $prototype = null);

    /**
     * Fetches organizations for a user
     *
     * @param Where|UserInterface|string $user
     * @param mixed $prototype
     * @return DbSelect
     */
    public function fetchOrganizationsForUser($user, $prototype = null);

    /**
     * Fetches all the users that a user has a relationship with
     *
     * @param $user
     * @param $where
     * @param null $prototype
     * @return DbSelect
     */
    public function fetchAllUsersForUser($user, $where = null, $prototype = null);
}
