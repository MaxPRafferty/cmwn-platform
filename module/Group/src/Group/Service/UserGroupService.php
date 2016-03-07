<?php

namespace Group\Service;

use Group\GroupInterface;
use User\UserInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Service to manage attaching users to groups
 *
 * @package Group\Service
 */
class UserGroupService implements UserGroupServiceInterface
{
    /**
     * @var TableGateway
     */
    protected $pivotTable;

    /**
     * GroupService constructor.
     * @param TableGateway $pivotTable
     */
    public function __construct(TableGateway $pivotTable)
    {
        $this->pivotTable = $pivotTable;
    }

    /**
     * Attaches a user to a group
     *
     * @param GroupInterface $group
     * @param UserInterface $user
     * @param $role
     * @return bool
     * @throws \RuntimeException
     */
    public function attachUserToGroup(GroupInterface $group, UserInterface $user, $role)
    {
        if (!is_string($role) && !$role instanceof RoleInterface) {
            throw new \RuntimeException('Role must either be a sting or instance of Zend\PermissionAcl\RoleInterface');
        }

        $role = $role instanceof RoleInterface ? $role->getRoleId() : $role;

        $this->pivotTable->insert([
            'user_id'  => $user->getUserId(),
            'group_id' => $group->getGroupId(),
            'role'     => $role
        ]);

        return true;
    }

    /**
     * Detaches a user from a group
     *
     * @param GroupInterface $group
     * @param UserInterface $user
     * @return bool
     */
    public function detachUserFromGroup(GroupInterface $group, UserInterface $user)
    {
        $this->pivotTable->delete([
            'user_id'  => $user->getUserId(),
            'group_id' => $group->getGroupId()
        ]);

        return true;
    }
}
