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
 * This class determines the role of a user in a given organization
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
