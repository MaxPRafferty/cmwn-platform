<?php

namespace Group\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Group\Group;
use Ramsey\Uuid\Uuid;
use Group\GroupInterface;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Between;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Json\Json;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class GroupService
 * @package Group\Service
 */
class GroupService implements GroupServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $groupTableGateway;

    /**
     * GroupService constructor.
     *
     * @param TableGateway $gateway
     */
    public function __construct(TableGateway $gateway)
    {
        $this->groupTableGateway = $gateway;
    }

    /**
     * @param GroupInterface $parent
     * @param GroupInterface $child
     * @return bool
     */
    public function addChildToGroup(GroupInterface $parent, GroupInterface $child)
    {
        $child->setParentId($parent);
        $this->saveGroup($child);

        // fetch the parent to get the latest left value
        $parent->exchangeArray($this->fetchGroup($parent->getGroupId())->getArrayCopy());

        // if the left for the parent is 0, then both are not in the tree so make them a tree
        if ($parent->getLeft() < 1) {
            $this->groupTableGateway->update(
                ['lft' => 1, 'rgt' => 4],
                ['group_id' => $parent->getGroupId()]
            );

            $this->groupTableGateway->update(
                ['lft' => 2, 'rgt' => 3],
                ['group_id' => $child->getGroupId()]
            );

            return true;
        }

        // UPDATE group SET rgt = rgt + 2 WHERE rgt > @lft AND org_id = @org_id
        // UPDATE group SET lft = lft + 2 WHERE lft > @lft AND org_id = @org_id

        // TODO create transaction
        $where = new Where();
        $where->addPredicate(new Operator('rgt', Operator::OP_GT, $parent->getLeft()));
        $where->addPredicate(new Operator('organization_id', Operator::OP_EQ, $parent->getOrganizationId()));
        $this->groupTableGateway->update(
            ['rgt' => new Expression("rgt + 2")],
            $where
        );

        $where = new Where();
        $where->addPredicate(new Operator('lft', Operator::OP_GT, $parent->getLeft()));
        $where->addPredicate(new Operator('organization_id', Operator::OP_EQ, $parent->getOrganizationId()));
        $where->addPredicate(new Operator('group_id', Operator::OP_NE, $parent->getGroupId()));
        $this->groupTableGateway->update(
            ['lft' => new Expression('lft + 2')],
            $where
        );

        // UPDATE group SET rgt = $parent->getLeft() + 1, rgt = $parent->getLeft() + 2 WHERE group_id = $child->getGroupid()

        $where = new Where();
        $where->addPredicate(new Operator('group_id', Operator::OP_EQ, $child->getGroupId()));
        $this->groupTableGateway->update(
            ['lft' => $parent->getLeft() + 1, 'rgt' => $parent->getLeft() + 2],
            $where
        );

        return true;
    }

    /**
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where     = $this->createWhere($where);
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);

        if ($paginate) {
            $select    = new Select($this->groupTableGateway->getTable());
            $select->where($where);
            return new DbSelect(
                $select,
                $this->groupTableGateway->getAdapter(),
                $resultSet
            );
        }

        $results = $this->groupTableGateway->select($where);
        $resultSet->initialize($results);
        return $resultSet;
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
    public function fetchAllForUser($user, $where = null, $paginate = true, $prototype = null)
    {
        $where = $this->createWhere($where);

        if ($user instanceof UserInterface) {
            $where->addPredicate(new Operator('ug.user_id', Operator::OP_EQ, $user->getUserId()));
        }

        if (is_string($user)) {
            $where->addPredicate(new Operator('ug.user_id', Operator::OP_EQ, $user));
        }

        $select = new Select();
        $select->columns(['g' => '*']);
        $select->from(['g'  => 'groups']);
        $select->join(['ug' => 'user_groups'], 'ug.group_id = g.group_id', [], Select::JOIN_LEFT);
        $select->where($where);

        $sql = new Sql($this->groupTableGateway->getAdapter());
        $stmt = $sql->prepareStatementForSqlObject($select);
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);
        if ($paginate) {
            return new DbSelect(
                $select,
                $this->groupTableGateway->getAdapter(),
                $resultSet
            );
        }

        $results = $this->groupTableGateway->select($select);
        $resultSet->initialize($results);
        return $resultSet;
    }

    /**
     * Saves a group
     *
     * If the group id is null, then a new group is created
     *
     * @param GroupInterface $group
     * @return bool
     * @throws NotFoundException
     */
    public function saveGroup(GroupInterface $group)
    {
        $new = empty($group->getGroupId());
        $group->setUpdated(new \DateTime());
        $data = $group->getArrayCopy();

        $data['meta'] = Json::encode($data['meta']);
        $data['lft'] = $group->getLeft();
        $data['rgt'] = $group->getRight();

        unset($data['left']);
        unset($data['right']);
        unset($data['depth']);
        unset($data['deleted']);

        if ($new) {
            $group->setCreated(new \DateTime());
            $group->setGroupId(Uuid::uuid1());

            $data['group_id'] = $group->getGroupId();
            $data['created'] = $group->getCreated()->format(\DateTime::ISO8601);

            $this->groupTableGateway->insert($data);
            return true;
        }

        $this->fetchGroup($group->getGroupId());

        $this->groupTableGateway->update(
            $data,
            ['group_id' => $group->getGroupId()]
        );

        return true;
    }

    /**
     * Fetches one group from the DB using the id
     *
     * @param $groupId
     * @return GroupInterface
     * @throws NotFoundException
     */
    public function fetchGroup($groupId)
    {
        $rowset = $this->groupTableGateway->select(['group_id' => $groupId]);
        $row    = $rowset->current();
        if (!$row) {
            throw new NotFoundException("Group not Found");
        }

        return new Group($row->getArrayCopy());
    }

    /**
     * Fetches on group from the DB by using the external id
     *
     * @param $externalId
     * @return GroupInterface
     * @throws NotFoundException
     */
    public function fetchGroupByExternalId($externalId)
    {
        $rowset = $this->groupTableGateway->select(['external_id' => $externalId]);
        $row    = $rowset->current();
        if (!$row) {
            throw new NotFoundException("Group not Found");
        }

        return new Group($row->getArrayCopy());
    }

    /**
     * Deletes a group from the database
     *
     * Soft deletes unless soft is false
     *
     * @param GroupInterface $group
     * @param bool $soft
     * @return bool
     */
    public function deleteGroup(GroupInterface $group, $soft = true)
    {
        $this->fetchGroup($group->getGroupId());

        if ($soft) {
            $group->setDeleted(new \DateTime());

            $this->groupTableGateway->update(
                ['deleted'  => $group->getDeleted()->format(\DateTime::ISO8601)],
                ['group_id' => $group->getGroupId()]
            );

            return true;
        }

        $this->groupTableGateway->delete(['group_id' => $group->getGroupId()]);
        return true;
    }

    /**
     * Fethes all the types of groups for the children
     *
     * Used for hal link building
     *
     * @param GroupInterface $group
     * @return string[]
     */
    public function fetchChildTypes(GroupInterface $group)
    {
        if (!$group->hasChildren()) {
            return [];
        }

        $select = new Select();
        $select->columns([new Expression('DISTINCT(type) AS type')]);
        $select->from($this->groupTableGateway->getTable());
        $where = new Where();

        $where->addPredicate(new Operator('organization_id', '=', $group->getOrganizationId()));
        $where->addPredicate(new Between('lft', ($group->getLeft() + 1), ($group->getRight() - 1)));

        $select->where($where);

        $results = $this->groupTableGateway->selectWith($select);
        $types   = [];
        foreach ($results as $row) {
            $types[] = $row['type'];
        }

        sort($types);
        return array_unique($types);
    }

    /**
     * Fetches all the children groups for a given group
     *
     * @param GroupInterface $group
     * @param null|PredicateInterface|array $where
     * @param null|object $prototype
     * @return DbSelect
     */
    public function fetchChildGroups(GroupInterface $group, $where = null, $prototype = null)
    {
        $where  = $this->createWhere($where);
        $select = new Select();
        $select->from($this->groupTableGateway->getTable());

        $where->addPredicate(new Operator('organization_id', '=', $group->getOrganizationId()));
        $where->addPredicate(new Between('lft', ($group->getLeft() + 1), ($group->getRight() - 1)));
        $select->where($where);

        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);
        return new DbSelect(
            $select,
            $this->groupTableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * Fetches all the types of groups
     *
     * @return string[]
     */
    public function fetchGroupTypes()
    {
        $select = new Select();
        $select->columns([new Expression('DISTINCT(type) AS type')]);
        $select->from($this->groupTableGateway->getTable());

        $results = $this->groupTableGateway->selectWith($select);
        $types   = [];
        foreach ($results as $row) {
            $types[] = $row['type'];
        }

        sort($types);
        return array_unique($types);
    }
}
