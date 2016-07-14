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
class SecurityOrgService
{
    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * SecurityOrgService constructor.
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Gets the role from the group
     *
     * SELECT ug.role,
     *      active_group.group_id AS active_group_id,
     *      parent_group.group_id as parent_group_id,
     *      g.*
     * FROM user_groups AS ug
     *  LEFT JOIN groups AS active_group ON active_group.group_id = ug.group_id
     *  LEFT JOIN groups AS parent_group ON parent_group.group_id = active_group.parent_id
     *  LEFT OUTER JOIN groups AS g
     *      ON (g.head BETWEEN active_group.head AND active_group.tail)
     *      OR (g.group_id = parent_group.group_id)
     * WHERE ug.user_id = 'english_teacher'
     *  AND g.group_id = 'school'
     *  AND g.organization_id = active_group.organization_id
     * LIMIT 1
     *
     * @param $user
     * @param $group
     * @thought Move to own service?
     */
    public function getRoleForGroup($group, $user)
    {
        $groupId = $group instanceof GroupInterface ? $group->getGroupId() : $group;
        $userId  = $user instanceof UserInterface ? $user->getUserId() : $user;

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
            new Expression('g.organization_id = active_group.organization_id')
        );

        $select->where($where);
        $sql   = new Sql($this->adapter);
        $stmt  = $sql->prepareStatementForSqlObject($select);
        $results  = $stmt->execute();

        $results->rewind();
        $role = $results->current()['role'];
        return $role;
    }

    /**
     * Gets the role for the org
     *
     * SELECT ug.role AS role
     * FROM groups g
     *   LEFT JOIN user_groups ug ON ug.group_id = g.group_id
     * WHERE g.organization_id = '27d713fe-f206-11e5-b2bc-209a2c42dc83'
     *   AND ug.user_id = '6bfb2d84-f299-11e5-b53c-0800274f2cef'
     * ORDER BY g.lft ASC
     * LIMIT 1
     *
     * @param $user
     * @param $org
     * @thought Move to own service?
     */
    public function getRoleForOrg($org, $user)
    {
        $orgId  = $org instanceof OrganizationInterface ? $org->getOrgId() : $org;
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;

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
        return $role;
    }
}
