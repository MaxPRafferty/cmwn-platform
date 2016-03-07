<?php

namespace Group\Service;

use Group\GroupInterface;
use User\UserInterface;
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
}
