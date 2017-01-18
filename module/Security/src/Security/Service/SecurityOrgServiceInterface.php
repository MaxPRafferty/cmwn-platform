<?php

namespace Security\Service;

use User\UserInterface;
use Org\OrganizationInterface;

/**
 * Interface SecurityOrgServiceInterface
 */
interface SecurityOrgServiceInterface
{
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
