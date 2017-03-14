<?php

namespace Group\Service;

use Application\Utils\ServiceTrait;
use Group\Exception\RuntimeException;
use Group\Group;
use Group\GroupInterface;
use Org\Organization;
use Org\OrganizationInterface;
use User\UserHydrator;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Permissions\Rbac\RoleInterface;

/**
 * A Service that deals with users in groups
 */
class UserGroupService implements UserGroupServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $pivotTable;

    /**
     * @var UserHydrator
     */
    protected $hydrator;

    /**
     * GroupService constructor.
     *
     * @param TableGateway $pivotTable
     */
    public function __construct(TableGateway $pivotTable)
    {
        $this->pivotTable = $pivotTable;
        $this->hydrator   = new UserHydrator();
    }

    /**
     * @inheritdoc
     */
    public function getAlias(): string
    {
        return 'g';
    }

    /**
     * @inheritdoc
     */
    public function attachUserToGroup(GroupInterface $group, UserInterface $user, $role): bool
    {
        $role = $role instanceof RoleInterface ? $role->getRoleId() : $role;

        if (empty($role)) {
            throw new RuntimeException('Role must either be a sting or instance of ' . RoleInterface::class);
        }

        $this->pivotTable->insert([
            'user_id'  => $user->getUserId(),
            'group_id' => $group->getGroupId(),
            'role'     => $role,
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function detachUserFromGroup(GroupInterface $group, UserInterface $user): bool
    {
        $this->pivotTable->delete([
            'user_id'  => $user->getUserId(),
            'group_id' => $group->getGroupId(),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function fetchUsersForGroup(
        GroupInterface $group,
        $where = null,
        UserInterface $prototype = null
    ): AdapterInterface {
        $where = $this->createWhere($where);
        $where->addPredicate(new Operator('g.group_id', '=', $group->getGroupId()));

        $select = new Select(['g' => 'groups']);
        $select->columns(['active_group' => 'group_id']);

        // Get all the child groups
        $select->join(
            ['cg' => 'groups'],
            '(cg.head BETWEEN g.head AND g.tail) AND (cg.network_id = g.network_id)',
            ['child_group' => 'group_id'],
            Select::JOIN_LEFT_OUTER
        );

        // Get all the users in the groups we just got
        $select->join(
            ['ug' => 'user_groups'],
            'ug.group_id = cg.group_id',
            ['user_group_id' => 'group_id'],
            Select::JOIN_LEFT_OUTER
        );

        // Get all the users
        $select->join(
            ['u' => 'users'],
            'ug.user_id = u.user_id',
            ['*'],
            Select::JOIN_LEFT_OUTER
        );

        $select->where($where);
        $select->order(['u.first_name', 'u.last_name']);
        $select->group(['u.user_id']);
        $prototype = $prototype ?? new \stdClass();
        $resultSet = new HydratingResultSet($this->hydrator, $prototype);

        return new DbSelect(
            $select,
            $this->pivotTable->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function fetchUsersForOrg(
        OrganizationInterface $organization,
        $where = null,
        UserInterface $prototype = null
    ): AdapterInterface {
        $where = $this->createWhere($where);
        $where->addPredicate(new Operator('g.organization_id', Operator::OP_EQ, $organization->getOrgId()));

        $select = new Select(['g' => 'groups']);
        $select->columns(['group_id']);

        // join in the groups
        $select->join(
            ['ug' => 'user_groups'],
            'ug.group_id = g.group_id',
            ['user_group_id' => 'group_id'],
            Select::JOIN_LEFT_OUTER
        );

        // join in the users
        $select->join(
            ['u' => 'users'],
            'ug.user_id = u.user_id',
            ['*'],
            Select::JOIN_LEFT_OUTER
        );

        $select->where($where);
        $select->group('u.user_id');
        $select->order(['u.first_name', 'u.last_name']);

        $prototype = $prototype ?? new \stdClass();
        $resultSet = new HydratingResultSet($this->hydrator, $prototype);

        return new DbSelect(
            $select,
            $this->pivotTable->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function fetchGroupsForUser(
        UserInterface $user,
        $where = null,
        GroupInterface $prototype = null
    ): AdapterInterface {

        $where = $this->createWhere($where);
        $where->addPredicate(new Operator('ug.user_id', '=', $user->getUserId()));

        $select = new Select(['ug' => 'user_groups']);
        $select->columns(['ug_role' => 'role']);

        // This grabs all the groups the user belongs too
        $select->join(
            ['ugg' => 'groups'],
            'ugg.group_id = ug.group_id',
            ['user_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

        // This grabs all the sub groups from the above groups
        $select->join(
            ['sg' => 'groups'],
            'sg.head BETWEEN ugg.head AND ugg.tail ' .
            'AND sg.network_id = ugg.network_id',
            ['sub_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

        // This grabs the group data
        $select->join(
            ['g' => 'groups'],
            'g.group_id = sg.group_id OR g.group_id = ugg.parent_id',
            '*',
            Select::JOIN_LEFT
        );

        $select->where($where);
        // with all the joins we are bound to get some duplicates
        $select->group('g.group_id');
        $select->order(['g.title']);

        $prototype = $prototype ?? new Group();
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);

        $sql = new \Zend\Db\Sql\Sql($this->pivotTable->getAdapter());
        $stmt = $sql->buildSqlString($select);
        return new DbSelect(
            $select,
            $this->pivotTable->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function fetchOrganizationsForUser(
        UserInterface $user,
        OrganizationInterface $prototype = null
    ): AdapterInterface {
        $where = $this->createWhere([]);
        $where->addPredicate(new Operator('ug.user_id', Operator::OP_EQ, $user->getUserId()));

        $select = new Select();
        $select->columns(['o' => '*']);
        $select->from(['o' => 'organizations']);

        // Join in the groups to get the organization id
        $select->join(
            ['g' => 'groups'],
            'o.org_id = g.organization_id',
            ['real_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

        // Join in the groups the user belongs too
        $select->join(
            ['ug' => 'user_groups'],
            'ug.group_id = g.group_id',
            ['ug_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

        $select->where($where);
        $select->group('o.org_id');
        $select->order('o.title ASC');
        $prototype = $prototype ?? new Organization();
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);

        return new DbSelect(
            $select,
            $this->pivotTable->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function fetchAllUsersForUser(
        UserInterface $user,
        $where = null,
        UserInterface $prototype = null
    ): AdapterInterface {
        $select = new Select(['ug' => 'user_groups']);
        $select->columns(['ug_role' => 'role']);

        // This is the groups that $userId belongs too
        $select->join(
            ['ugg' => 'groups'],
            'ugg.group_id = ug.group_id',
            ['user_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

        // This is all the sub groups from above
        $select->join(
            ['sg' => 'groups'],
            'sg.network_id = ugg.network_id AND sg.head BETWEEN ugg.head AND ugg.tail',
            ['sub_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

        // This is all the groups our user belongs too
        $select->join(
            ['g' => 'groups'],
            'g.group_id = sg.group_id OR g.group_id = ugg.parent_id',
            ['real_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

        // This is all the groups other users belong too
        $select->join(
            ['oug' => 'user_groups'],
            'oug.group_id = g.group_id',
            ['other_group_id' => 'group_id'],
            Select::JOIN_LEFT_OUTER
        );

        // This includes all the friends
        $select->join(
            ['uf' => 'user_friends'],
            'uf.user_id = ug.user_id OR uf.friend_id = ug.user_id',
            ['friend_status' => 'status'],
            Select::JOIN_LEFT_OUTER
        );

        // Finially we come to what we really want, and that is the users
        $select->join(
            ['u' => 'users'],
            'u.user_id = oug.user_id OR u.user_id = uf.friend_id OR u.user_id = uf.user_id',
            '*',
            Select::JOIN_LEFT_OUTER
        );

        $where = $this->createWhere($where);
        $where->addPredicate(new Operator('ug.user_id', '=', $user->getUserId()));
        $select->where($where);
        $select->group(['u.user_id']);
        $select->having(new Operator('u.user_id', '!=', $user->getUserId()));
        $select->order(['u.first_name', 'u.last_name']);

        $prototype = $prototype ?? new \stdClass();
        $resultSet = new HydratingResultSet($this->hydrator, $prototype);

        return new DbSelect(
            $select,
            $this->pivotTable->getAdapter(),
            $resultSet
        );
    }
}
