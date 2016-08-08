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
     * SELECT
     *   ug.group_id
     *   u.*
     * FROM user_groups
     *   LEFT OUTER JOIN users AS u ON ug.user_id = u.user_id
     * WHERE ug.group_id = :group_id
     * ORDER BY u.first_name, u.last_name
     *
     * @param Where|GroupInterface|string $group
     * @param array $where
     * @param object $prototype
     *
     * @return DbSelect
     */
    public function fetchUsersForGroup(GroupInterface $group, $where = null, $prototype = null);

    /**
     *
     * Finds all the users for an organization
     *
     * SELECT u.*
     * FROM groups g
     *   LEFT OUTER JOIN user_groups AS ug ON ug.group_id = g.group_id
     *   LEFT OUTER JOIN users AS u ON ug.user_id = u.user_id
     * WHERE g.organization_id = :org_id
     * GROUP BY u.user_id
     * ORDER BY u.first_name, u.last_name
     *
     * @param $organization
     * @param array $where
     * @param null $prototype
     *
     * @return DbSelect
     */
    public function fetchUsersForOrg($organization, $where = null, $prototype = null);

    /**
     * Finds all the groups for a user
     *
     * SELECT
     *      ug.role      AS ug_role,
     *      ugg.group_id AS user_group_id,
     *      sg.group_id  AS sub_group_id,
     *      g.*
     * FROM user_groups AS ug
     *      LEFT JOIN groups AS ugg ON ugg.group_id = ug.group_id
     *      LEFT JOIN groups AS sg ON sg.head BETWEEN ugg.head AND ugg.tail
     *          AND sg.network_id = ugg.network_id
     *      LEFT JOIN groups AS g ON g.group_id = sg.group_id OR g.group_id = ugg.parent_id
     * WHERE ug.user_id = :user_id
     * GROUP BY g.group_id
     * ORDER BY g.title ASC;
     *
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
