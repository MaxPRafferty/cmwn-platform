<?php

namespace Security\Service;

use User\UserInterface;
use Group\GroupInterface;
use Org\OrganizationInterface;

/**
 * Interface SecurityOrgServiceInterface
 */
interface SecurityOrgServiceInterface
{
    /**
     * Finds the role for a user in a specific group
     *
     * SELECT ug.role AS role
     * FROM user_groups AS ug
     *  LEFT JOIN groups AS active_group ON active_group.group_id = ug.group_id
     *  LEFT JOIN groups AS parent_group ON parent_group.group_id = active_group.parent_id
     *  LEFT JOIN groups AS g ON  (g.head BETWEEN active_group.head AND active_group.tail)
     *      OR (g.group_id = parent_group.group_id)
     * WHERE ug.user_id = :user_id
     *  AND g.group_id = :group_id
     *  AND g.network_id = active_group.network_id
     *
     * @param GroupInterface|string $group the group you want
     * @param UserInterface $user the user you want to get
     *
     * @return string
     */
    public function getRoleForGroup($group, UserInterface $user);

    /**
     * Finds the role the user has at the top level of the organization
     *
     * SELECT ug.role AS role
     * FROM groups g
     *  LEFT JOIN user_groups ug ON ug.group_id = g.group_id
     * WHERE ug.user_id = :user_id
     *  AND g.organization_id = :organization_id
     * ORDER BY g.head ASC
     * LIMIT 1
     *
     * @param OrganizationInterface|string $org the Organization to Check for
     * @param UserInterface $user The user to find
     *
     * @return string
     */
    public function getRoleForOrg($org, UserInterface $user);
}
