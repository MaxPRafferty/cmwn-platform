<?php

namespace Security\Service;

use Group\GroupInterface;
use Org\OrganizationInterface;
use User\UserInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

/**
 * Class SecurityOrgService
 *
 * @package Security\Service
 */
class SecurityOrgService implements SecurityOrgServiceInterface
{
    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * SecurityOrgService constructor.
     *
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @inheritdoc
     */
    public function getRoleForGroup($group, UserInterface $user)
    {
        $groupId = $group instanceof GroupInterface ? $group->getGroupId() : $group;
        $userId  = $user->getUserId();

        $select = new Select(['ug' => 'user_groups']);
        $select->columns(['role' => 'ug.role'], false);

        // Get all the groups the user is assigned too
        $select->join(
            ['active_group' => 'groups'],
            'active_group.group_id = ug.group_id',
            ['active_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

        // Get the parent group in case the user is one group up
        $select->join(
            ['parent_group' => 'groups'],
            'parent_group.group_id = active_group.parent_id',
            ['parent_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

        // Get all the groups in the network
        $select->join(
            ['g' => 'groups'],
            '(g.head BETWEEN active_group.head AND active_group.tail) OR (g.group_id = parent_group.group_id)',
            ['g' => 'group_id'],
            Select::JOIN_LEFT_OUTER
        );

        $where = new Where();

        // Get groups the $user belongs too
        $where->addPredicate(new Operator('ug.user_id', '=', $userId));

        // We only want the group we are looking at
        $where->addPredicate(new Operator('g.group_id', '=', $groupId));

        // Preserve the tree
        $where->addPredicate(
            new Expression('g.network_id = active_group.network_id')
        );

        $select->where($where);
        $sql     = new Sql($this->adapter);
        $stmt    = $sql->prepareStatementForSqlObject($select);
        $results = $stmt->execute();

        $results->rewind();
        $role = $results->current()['role'];
        $role = $role === null ? 'logged_in' : $role;

        return $role . '.' . strtolower($user->getType());
    }

    /**
     * @inheritdoc
     */
    public function getRoleForOrg($org, UserInterface $user)
    {
        $orgId  = $org instanceof OrganizationInterface ? $org->getOrgId() : $org;
        $userId = $user->getUserId();

        $select = new Select();
        $select->columns(['role' => 'ug.role'], false);
        $select->from(['g' => 'groups']);
        $select->join(['ug' => 'user_groups'], 'ug.group_id = g.group_id', [], Select::JOIN_LEFT);
        $select->order('g.head ASC');
        $select->limit(1);

        $where = new Where();

        $where->addPredicate(new Operator('ug.user_id', '=', $userId));
        $where->addPredicate(new Operator('g.organization_id', '=', $orgId));

        $select->where($where);
        $sql     = new Sql($this->adapter);
        $stmt    = $sql->prepareStatementForSqlObject($select);
        $results = $stmt->execute();

        $results->rewind();
        $role = $results->current()['role'];
        $role = $role === null ? 'logged_in' : $role;

        return $role . '.' . strtolower($user->getType());
    }
}
