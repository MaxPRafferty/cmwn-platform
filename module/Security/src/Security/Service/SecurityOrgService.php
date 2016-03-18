<?php

namespace Security\Service;

use Group\GroupInterface;
use Org\Organization;
use Security\SecurityUser;
use User\UserInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Between;
use Zend\Db\Sql\Predicate\In;
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
     * Attaches the 1st 10 organizations to a logged in user
     *
     * SELECT
     *   DISTINCT(o.org_id) AS org_id,
     *   o.type AS org_type,
     *   o.description AS org_descrption
     * FROM organizations o
     *   LEFT JOIN groups g ON o.org_id = g.organization_id
     *   LEFT JOIN user_groups ug ON ug.group_id = g.group_id
     * WHERE ug.user_id = 'b4e9147a-e60a-11e5-b8ea-0800274f2cef'
     * ORDER BY org_id ASC
     * LIMIT 10;
     *
     * @param SecurityUser $user
     * @return $this
     * @todo Cache
     */
    public function attachOrganizationsToUser(SecurityUser $user)
    {
        if ($user->isSuper()) {
            return $this;
        }

        $select = new Select();
        $select->columns([
            new Expression('DISTINCT(o.org_id) AS org_id'),
            'title AS title',
            'type AS type',
            'description AS description'
        ]);

        $where = new Where();
        $where->addPredicate(new Operator('ug.user_id', Operator::OP_EQ, $user->getUserId()));

        $select->from(['o'  => 'organizations']);
        $select->join(['g'  => 'groups'], 'o.org_id = g.organization_id', [], Select::JOIN_LEFT);
        $select->join(['ug' => 'user_groups'], 'ug.group_id = g.group_id', [], Select::JOIN_LEFT);
        $select->where($where);
        $select->order('org_id ASC');
        $select->limit(10);

        $sql  = new Sql($this->adapter);
        $stmt = $sql->prepareStatementForSqlObject($select);
        $orgs = $stmt->execute();

        foreach ($orgs as $orgData) {
            $user->addOrganization(new Organization($orgData));
        }

        return $this;
    }

    /**
     * Attaches the group types to a logged in user
     *
     * SELECT
     * DISTINCT g.type
     * FROM groups g
     *   LEFT JOIN user_groups ug ON ug.group_id = g.group_id
     * WHERE ug.user_id = 'b4e9147a-e60a-11e5-b8ea-0800274f2cef'
     *   AND g.organization_id IN ('6bb4900e-e605-11e5-8c29-0800274f2cef', '6bb41c82-e605-11e5-a7a7-0800274f2cef');
     *
     * @param SecurityUser $user
     * @return $this
     * @todo Cache
     */
    public function attachGroupTypesToUser(SecurityUser $user)
    {
        if ($user->isSuper()) {
            return $this;
        }

        $orgIds = array_keys($user->getOrganizations());

        if (empty($orgIds)) {
            return $this;
        }

        $select = new Select();
        $select->columns([
            new Expression('DISTINCT(g.type) AS type'),
        ]);

        $where = new Where();
        // FIXME When orgIds empty just continue;
        $where->addPredicate(new In('organization_id', $orgIds));

        $select->from(['g'  => 'groups']);
        $select->join(['ug' => 'user_groups'], 'ug.group_id = g.group_id', [], Select::JOIN_LEFT);
        $select->where($where);
        $select->limit(10);

        $sql   = new Sql($this->adapter);
        $stmt  = $sql->prepareStatementForSqlObject($select);
        $types = $stmt->execute();

        foreach ($types as $groupType) {
            $user->addGroupType($groupType['type']);
        }

        return $this;
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
     * @thought Move to own service?
     */
    public function getRoleForGroup($group, $user)
    {
        $groupId = $group instanceof GroupInterface ? $group->getGroupId() : $group;
        $userId  = $user instanceof UserInterface ? $user->getUserId() : $user;

        $select = new Select();
        $select->columns(['role' => 'ug.role'], false);
        $select->from(['parent' => 'groups']);
        $select->join(['node' => 'groups'], 'node.lft BETWEEN parent.lft AND parent.rgt');
        $select->join(['ug' => 'user_groups'], 'ug.group_id = parent.group_id', [], Select::JOIN_LEFT);
        $where = new Where();

        $where->addPredicate(new Operator('ug.user_id', '=', $userId));
        $where->addPredicate(new Operator('node.group_id', '=', $groupId));

        $select->where($where);
        $sql   = new Sql($this->adapter);
        $stmt  = $sql->prepareStatementForSqlObject($select);
        $results  = $stmt->execute();

        $results->rewind();
        $role = $results->current()['role'];
        return $role;
    }
}
