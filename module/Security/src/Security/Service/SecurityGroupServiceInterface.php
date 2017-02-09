<?php

namespace Security\Service;

use Group\GroupInterface;
use User\UserInterface;

/**
 * Interface SecurityGroupServiceInterface
 * @package Security\Service
 */
interface SecurityGroupServiceInterface
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
}
