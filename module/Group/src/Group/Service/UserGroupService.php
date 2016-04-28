<?php

namespace Group\Service;

use Application\Utils\ServiceTrait;
use Group\GroupInterface;
use Org\OrganizationInterface;
use User\UserHydrator;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Between;
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
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * SELECT u.*
     * FROM groups g
     *   LEFT JOIN groups AS active_group ON active_group.group_id = 'school'
     *   LEFT OUTER JOIN user_groups AS ug ON ug.group_id = g.group_id
     *   LEFT OUTER JOIN users AS u ON ug.user_id = u.user_id
     * WHERE g.head BETWEEN active_group.head AND active_group.tail
     *   AND g.organization_id = 'district'
     *
     * @param Where|GroupInterface|string $group
     * @param object $prototype
     * @return DbSelect
     */
    public function fetchUsersForGroup(GroupInterface $group, $prototype = null)
    {
        $where = $this->createWhere([]);
        $where->addPredicate(new Between(
            'g.head',
            new Expression('active_group.head'),
            new Expression('active_group.tail')
        ));

        $where->addPredicate(new Operator(
            'g.organization_id',
            '=',
            $group->getOrganizationId()
        ));

        $select = new Select(['g'  => 'groups']);
        $select->columns(['group_id']);
        $select->join(
            ['active_group' => 'groups'],
            new Expression('active_group.group_id = ?', $group->getGroupId()),
            ['active_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

        $select->join(
            ['ug' => 'user_groups'],
            'ug.group_id = g.group_id',
            ['user_group_id' => 'group_id'],
            Select::JOIN_LEFT_OUTER
        );

        $select->join(
            ['u' => 'users'],
            'ug.user_id = u.user_id',
            ['*'],
            Select::JOIN_LEFT_OUTER
        );

        $select->where($where);
        $select->group('u.user_id');

        $hydrator = $prototype instanceof UserInterface ? new ArraySerializable() : new UserHydrator();
        $resultSet = new HydratingResultSet($hydrator, $prototype);
        return new DbSelect(
            $select,
            $this->pivotTable->getAdapter(),
            $resultSet
        );
    }

    /**
     * Finds all the users for an organization
     *
     * SELECT u.*
     * FROM groups g
     *   LEFT OUTER JOIN user_groups AS ug ON ug.group_id = g.group_id
     *   LEFT OUTER JOIN users AS u ON ug.user_id = u.user_id
     * WHERE g.organization_id = 'district'
     * GROUP BY u.user_id;
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
        
        $select = new Select(['g'  => 'groups']);
        $select->columns(['group_id']);

        $select->join(
            ['ug' => 'user_groups'],
            'ug.group_id = g.group_id',
            ['user_group_id' => 'group_id'],
            Select::JOIN_LEFT_OUTER
        );

        $select->join(
            ['u' => 'users'],
            'ug.user_id = u.user_id',
            ['*'],
            Select::JOIN_LEFT_OUTER
        );

        $select->where($where);
        $select->group('u.user_id');

        $hydrator = $prototype instanceof UserInterface ? new ArraySerializable() : new UserHydrator();
        $resultSet = new HydratingResultSet($hydrator, $prototype);
        return new DbSelect(
            $select,
            $this->pivotTable->getAdapter(),
            $resultSet
        );
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
        $select->join(['ug' => 'user_groups'], 'ug.group_id = g.group_id', ['ug_role' => 'role'], Select::JOIN_LEFT);
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
     * @param Where|UserInterface|string $user
     * @param bool $prototype
     * @return DbSelect
     */
    public function fetchOrganizationsForUser($user, $prototype = null)
    {
        $where = $this->createWhere($user);

        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $where->addPredicate(new Operator('ug.user_id', Operator::OP_EQ, $userId));

        $select = new Select();
        $select->columns(['o' => '*']);
        $select->from(['o'  => 'organizations']);
        $select->join(
            ['g' => 'groups'],
            'o.org_id = g.organization_id',
            ['real_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

        $select->join(
            ['ug' => 'user_groups'],
            'ug.group_id = g.group_id',
            ['ug_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

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

    /**
     * SELECT ug.user_id AS active_user_id,
     *   active_group.group_id AS active_group_id,
     *   ug2.user_id AS sub_user_id,
     *   uf.friend_id as friend_id,
     *   u.*
     * FROM user_groups AS ug
     *   LEFT JOIN groups AS active_group ON active_group.group_id = ug.group_id
     *   LEFT OUTER JOIN groups AS g ON g.head BETWEEN active_group.head AND active_group.tail
     *   LEFT OUTER JOIN user_groups AS ug2 ON ug2.group_id = g.group_id
     *   LEFT OUTER JOIN user_friends AS uf ON uf.user_id = 'english_student'
     *   LEFT JOIN users AS u ON u.user_id = ug2.user_id OR u.user_id = uf.friend_id OR u.user_id = uf.user_id
     * WHERE ug.user_id = 'english_student'
     *   AND g.organization_id = active_group.organization_id
     * GROUP BY u.user_id
     * HAVING u.user_id != :user_id;
     *
     * @param $user
     * @param $where
     * @param null $prototype
     * @return DbSelect
     */
    public function fetchAllUsersForUser($user, $where = null, $prototype = null)
    {
        $where  = $this->createWhere($where);
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;

        $where->addPredicate(new Operator('ug.user_id', '=', $userId));
        $where->addPredicate(new Operator('g.organization_id', '=', new Expression('active_group.organization_id')));

        $select = new Select(['ug' => 'user_groups']);
        $select->columns(['real_user_id' => 'user_id']);
        $select->join(
            ['active_group' => 'groups'],
            'active_group.group_id = ug.group_id',
            ['active_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

        $select->join(
            ['g' => 'groups'],
            'g.head BETWEEN active_group.head AND active_group.tail',
            ['user_group_id' => 'group_id'],
            Select::JOIN_LEFT_OUTER
        );

        $select->join(
            ['ug2' => 'user_groups'],
            'ug2.group_id = g.group_id',
            ['sub_user_id' => 'user_id'],
            Select::JOIN_LEFT_OUTER
        );

        $select->join(
            ['uf' => 'user_friends'],
            new Expression(
                'uf.user_id = ? OR uf.friend_id = ?',
                [$userId, $userId]
            ),
            ['friend_id' => 'friend_id'],
            Select::JOIN_LEFT_OUTER
        );
        
        $select->join(
            ['u' => 'users'],
            new Expression(
                'u.user_id = ug2.user_id OR u.user_id = uf.friend_id OR u.user_id = uf.user_id'
            ),
            ['*'],
            Select::JOIN_LEFT_OUTER
        );

        $select->where($where);
        $select->group(['u.user_id']);
        $select->having(new Operator('u.user_id', '!=', $userId));

        $hydrator = $prototype instanceof UserInterface ? new ArraySerializable() : new UserHydrator();
        $resultSet = new HydratingResultSet($hydrator, $prototype);
        return new DbSelect(
            $select,
            $this->pivotTable->getAdapter(),
            $resultSet
        );
    }
}
