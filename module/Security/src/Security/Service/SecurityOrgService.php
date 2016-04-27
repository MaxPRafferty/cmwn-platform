<?php

namespace Security\Service;

use Group\GroupInterface;
use Org\OrganizationInterface;

use User\UserInterface;
use Zend\Db\Adapter\Adapter;


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
     * SELECT `ug`.role AS `role`, `node`.*
     * FROM `groups` AS `parent`
     *    INNER JOIN `groups` AS `node` ON `node`.`lft` BETWEEN `parent`.`lft` AND `parent`.`rgt`
     *    LEFT OUTER JOIN `user_groups` AS `ug` ON `ug`.`group_id` = `node`.`group_id`
     * WHERE `ug`.`user_id` = '6bfb2d84-f299-11e5-b53c-0800274f2cef'
     *    AND `parent`.`group_id` = '27d713fe-f206-11e6-b2bc-209a2c42dc83'
     * ORDER BY node.lft ASC
     * LIMIT 1;
     *
     * @param $user
     * @param $group
     * @thought Move to own service?
     */
    public function getRoleForGroup($group, $user)
    {
        $groupId = $group instanceof GroupInterface ? $group->getGroupId() : $group;
        $userId  = $user instanceof UserInterface ? $user->getUserId() : $user;

        $select = new Select();
        $select->columns(['role' => 'ug.role'], false);
        $select->from(['parent' => 'groups']);
        $select->join(['node' => 'groups'], 'node.head BETWEEN parent.head AND parent.tail');
        $select->join(['ug' => 'user_groups'], 'ug.group_id = node.group_id', [], Select::JOIN_LEFT_OUTER);
        $select->order('node.head ASC');
        $where = new Where();

        $where->addPredicate(new Operator('ug.user_id', '=', $userId));
        $where->addPredicate(new Operator('parent.group_id', '=', $groupId));

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
