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
     * SELECT *
     * FROM users u
     * LEFT JOIN user_groups ug ON ug.user_id = u.user_id
     * LEFT JOIN groups g ON ug.group_id = g.group_id
     * WHERE g.group_id = 'baz-bat'
     *
     * @param Where|GroupInterface|string $group
     * @param object $prototype
     * @return DbSelect
     */
    public function fetchUsersForGroup(GroupInterface $group, $prototype = null);

    /**
     * Finds all the users for an organization
     *
     * SELECT *
     * FROM users u
     * LEFT JOIN user_groups ug ON ug.user_id = u.user_id
     * LEFT JOIN groups g ON ug.group_id = g.group_id
     * WHERE g.organization_id = 'foo-bar'
     *
     * @param $organization
     * @param null $prototype
     * @return DbSelect
     */
    public function fetchUsersForOrg($organization, $prototype = null);

    /**
     * Finds all the users for a group
     *
     * SELECT *
     * FROM users u
     * LEFT JOIN user_groups ug ON ug.user_id = u.user_id
     * LEFT JOIN groups g ON ug.group_id = g.group_id
     * WHERE g.group_id = 'baz-bat'
     *
     * @param Where|GroupInterface|string $user
     * @param object $prototype
     * @return DbSelect
     */
    public function fetchGroupsForUser($user, $prototype = null);

    /**
     * Fetches organizations for a user
     *
     * SELECT
     *   o.*
     * FROM organizations o
     *   LEFT JOIN groups g ON o.org_id = g.organization_id
     *   LEFT JOIN user_groups ug ON ug.group_id = g.group_id
     * WHERE ug.user_id = 'b4e9147a-e60a-11e5-b8ea-0800274f2cef'
     * GROUP BY o.org_id
     *
     * @param Where|GroupInterface|string $user
     * @return DbSelect
     */
    public function fetchOrganizationsForUser($user, $prototype = null);

    /**
     * Gets the groups types for a user
     *
     * SELECT
     * DISTINCT g.type
     * FROM user_groups ug
     *   LEFT JOIN groups g ON ug.group_id = g.group_id
     * WHERE ug.user_id = 'b4e9147a-e60a-11e5-b8ea-0800274f2cef'
     *
     * @param string|UserInterface $user
     * @return $this
     */
    public function fetchGroupTypesForUser($user);

    /**
     * Gets the org types for a user
     *
     * SELECT
     * DISTINCT o.type
     * FROM user_groups ug
     *   LEFT JOIN groups g ON ug.group_id = g.group_id
     *   LEFT JOIN organizations o ON o.org_id = g.organization_id
     * WHERE ug.user_id = 'b4e9147a-e60a-11e5-b8ea-0800274f2cef'
     *
     * @param string|UserInterface $user
     * @return $this
     */
    public function fetchOrgTypesForUser($user);
}
