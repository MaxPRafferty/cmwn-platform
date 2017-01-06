<?php

namespace Security\Service;

use User\UserInterface;

/**
 * A Service used to find the role between 2 users
 */
interface SecurityGroupServiceInterface
{
    /**
     * Finds the role the user has to another user
     *
     * SELECT
     *  requested_user.user_id AS requested_user_id,
     *
     *  active_user.role AS active_role,
     *
     *  active_group.head AS active_head,
     *  active_group.tail AS active_tail,
     *
     *  active_parent_group.head AS active_parent_head,
     *  active_parent_group.tail AS active_parent_tail,
     *  active_parent_group.group_id AS active_parent_group,
     *
     *  requested_group.head AS requested_head,
     *  requested_group.tail AS requested_tail,
     *  requested_group.group_id AS requested_group,
     *
     *  requested_parent_group.head AS requested_parent_head,
     *  requested_parent_group.tail AS requested_parent_tail,
     *  requested_parent_group.group_id AS requested_parent_group
     *
     * FROM user_groups AS active_user
     *  LEFT JOIN user_groups AS requested_user ON requested_user.user_id = :user_id
     *  LEFT JOIN groups AS active_group ON active_group.group_id = requested_user.group_id
     *  LEFT JOIN groups AS active_parent_group ON active_parent_group.group_id = active_group.parent_id
     *  LEFT JOIN groups AS requested_group ON requested_group.group_id = requested_user.group_id
     *  LEFT JOIN groups AS requested_parent_group ON requested_parent_group.group_id = requested_group.parent_id
     *
     * WHERE active_user.user_id = :user_id
     *  AND active_group.organization_id = requested_group.organization_id
     *
     *
     * @param UserInterface $activeUser
     * @param UserInterface|string $requestedUser
     *
     * @return string
     */
    public function fetchRelationshipRole(UserInterface $activeUser, $requestedUser);
}
