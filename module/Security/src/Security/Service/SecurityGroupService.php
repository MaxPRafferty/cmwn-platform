<?php

namespace Security\Service;

use Group\GroupInterface;
use User\UserInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\In;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

/**
 * Class SecurityGroupService
 *
 * ${CARET}
 */
class SecurityGroupService
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
     * SELECT ug.role
     * FROM groups AS node,
     *   groups AS parent
     * LEFT JOIN user_groups ug ON ug.group_id = parent.group_id
     * WHERE node.lft BETWEEN parent.lft AND parent.rgt
     *   AND ug.user_id = 'f79b214a-ebba-11e5-86c6-0800274f2cef'
     *   AND node.group_id = '4463c692-ebae-11e5-8d27-0800274f2cef'
     * GROUP BY node.title
     * ORDER BY node.lft;
     *
     * @param $user
     * @param $group
     * @thought Move to own service
     */
    public function getRoleForGroup($group, $user)
    {
        $groupId = $group instanceof GroupInterface ? $group->getGroupId() : $group;
        $userId  = $user instanceof UserInterface ? $user->getUserId() : $user;

        $select = new Select();
        $select->from(['node' => 'groups']);
        $select->from(['parent' => 'groups']);
        $select->join(['ug' => 'user_groups'], 'ug.group_id = g.group_id', [], Select::JOIN_LEFT);
    }
}
