<?php

namespace Group\Service;

use Application\Utils\ServiceTrait;
use Group\GroupInterface;
use Org\OrganizationInterface;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Service to manage attaching users to groups
 *
 * @package Group\Service
 */
class UserGroupService implements UserGroupServiceInterface
{
    use ServiceTrait;

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
    public function fetchUsersForGroup($group, $prototype = null)
    {
        $where = $this->createWhere($group);

        if ($group instanceof GroupInterface) {
            $where->addPredicate(new Operator('g.group_id', Operator::OP_EQ, $group->getGroupId()));
        }

        if (is_string($group)) {
            $where->addPredicate(new Operator('g.group_id', Operator::OP_EQ, $group));
        }

        $select = new Select();
        $select->from(['u'  => 'users']);
        $select->join(['ug' => 'user_groups'], 'ug.user_id = u.user_id', [], Select::JOIN_LEFT);
        $select->join(['g'  => 'groups'], 'g.group_id = ug.group_id', [], Select::JOIN_LEFT);
        $select->where($where);

        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);
        return new DbSelect(
            $select,
            $this->pivotTable->getAdapter(),
            $resultSet
        );
    }

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
    public function fetchUsersForOrg($organization, $prototype = null)
    {
        $orgId = $organization instanceof OrganizationInterface ? $organization->getOrgId() : $organization;
        $where = new Where();
        $where->addPredicate(new Operator('g.organization_id', Operator::OP_EQ, $orgId));

        return $this->fetchUsersForGroup($where, $prototype);
    }


    /**
     * Finds all the groups for a user
     *
     * SELECT *
     * FROM groups g
     * LEFT JOIN user_groups ug ON ug.group_id = g.group_id
     * WHERE ug.user_id = 'baz-bat'
     *
     * @param Where|GroupInterface|string $user
     * @param object $prototype
     * @return DbSelect
     */
    public function fetchGroupsForUser($user, $prototype = null)
    {
        $where = $this->createWhere($user);

        if ($user instanceof UserInterface) {
            $where->addPredicate(new Operator('ug.user_id', Operator::OP_EQ, $user->getUserId()));
        }

        if (is_string($user)) {
            $where->addPredicate(new Operator('ug.user_id', Operator::OP_EQ, $user));
        }

        $select = new Select();
        $select->from(['g'  => 'groups']);
        $select->join(['ug' => 'user_groups'], 'ug.group_id = g.group_id', [], Select::JOIN_LEFT);
        $select->where($where);

        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);
        return new DbSelect(
            $select,
            $this->pivotTable->getAdapter(),
            $resultSet
        );
    }

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
    public function fetchOrganizationsForUser($user, $prototype = null)
    {
        $where = $this->createWhere($user);

        if ($user instanceof UserInterface) {
            $where->addPredicate(new Operator('ug.user_id', Operator::OP_EQ, $user->getUserId()));
        }

        if (is_string($user)) {
            $where->addPredicate(new Operator('ug.user_id', Operator::OP_EQ, $user));
        }

        $select = new Select();
        $select->columns(['o' => '*']);
        $select->from(['o'  => 'organizations']);
        $select->join(['g'  => 'groups'], 'o.org_id = g.organization_id', [], Select::JOIN_LEFT);
        $select->join(['ug' => 'user_groups'], 'ug.group_id = g.group_id', [], Select::JOIN_LEFT);
        $select->where($where);
        $select->group('o.org_id');
        $select->order('org_id ASC');

        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);
        return new DbSelect(
            $select,
            $this->pivotTable->getAdapter(),
            $resultSet
        );
    }

}
