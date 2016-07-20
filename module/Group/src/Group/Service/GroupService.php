<?php

namespace Group\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Group\Group;
use Org\OrganizationInterface;
use Ramsey\Uuid\Uuid;
use Group\GroupInterface;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Between;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Json\Json;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class GroupService
 *
 * @package Group\Service
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
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
     *
     * @return bool
     */
    public function addChildToGroup(GroupInterface $parent, GroupInterface $child)
    {
        $child->setParentId($parent);
        $this->saveGroup($child);

        // fetch the parent to get the latest head value
        $parent->exchangeArray($this->fetchGroup($parent->getGroupId())->getArrayCopy());

        // if the head for the parent is 0, then both are not in the tree so make them a tree
        if ($parent->getHead() < 1) {
            $this->groupTableGateway->update(
                ['head' => 1, 'tail' => 4],
                ['group_id' => $parent->getGroupId()]
            );

            $this->groupTableGateway->update(
                ['head' => 2, 'tail' => 3],
                ['group_id' => $child->getGroupId()]
            );

            return true;
        }

        // UPDATE group SET tail = tail + 2 WHERE tail > @head AND org_id = @org_id
        // UPDATE group SET head = head + 2 WHERE head > @head AND org_id = @org_id

        // TODO create transaction
        $where = new Where();
        $where->addPredicate(new Operator('tail', Operator::OP_GT, $parent->getHead()));
        $where->addPredicate(new Operator('organization_id', Operator::OP_EQ, $parent->getOrganizationId()));
        $this->groupTableGateway->update(
            ['tail' => new Expression("tail + 2")],
            $where
        );

        $where = new Where();
        $where->addPredicate(new Operator('head', Operator::OP_GT, $parent->getHead()));
        $where->addPredicate(new Operator('organization_id', Operator::OP_EQ, $parent->getOrganizationId()));
        $where->addPredicate(new Operator('group_id', Operator::OP_NE, $parent->getGroupId()));
        $this->groupTableGateway->update(
            ['head' => new Expression('head + 2')],
            $where
        );

        $where = new Where();
        $where->addPredicate(new Operator('group_id', Operator::OP_EQ, $child->getGroupId()));
        $this->groupTableGateway->update(
            ['head' => $parent->getHead() + 1, 'tail' => $parent->getHead() + 2],
            $where
        );

        return true;
    }

    /**
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     *
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where     = $this->createWhere($where);
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);

        if ($paginate) {
            $select = new Select(['g' => $this->groupTableGateway->getTable()]);
            $select->where($where);
            $select->order(['g.title']);

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
     * @param UserInterface|string $user
     * @param Where|GroupInterface|string $where
     * @param object $prototype
     * @param bool $paginate
     *
     * @return DbSelect
     */
    public function fetchAllForUser($user, $where = null, $paginate = true, $prototype = null)
    {
        $where  = $this->createWhere($where);
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $where->addPredicate(new Operator('ug.user_id', '=', $userId));

        $select = new Select(['ug' => 'user_groups']);
        $select->columns([]);
        $select->join(
            ['active_group' => 'groups'],
            'active_group.group_id = ug.group_id',
            ['active_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

        $select->join(
            ['g' => 'groups'],
            new Expression('g.head BETWEEN active_group.head AND active_group.tail'),
            ['*'],
            Select::JOIN_LEFT_OUTER
        );

        $where->addPredicate(new Operator('g.organization_id', '=', new Expression('active_group.organization_id')));
        $select->where($where);
        $select->order(['g.title']);

        $prototype = $prototype === null ? new Group() : $prototype;
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
     *
     * @return bool
     * @throws NotFoundException
     */
    public function saveGroup(GroupInterface $group)
    {
        $new = empty($group->getGroupId());
        $group->setUpdated(new \DateTime());
        $data = $group->getArrayCopy();

        $data['meta'] = Json::encode($data['meta']);
        $data['tail'] = $group->getTail();

        unset($data['depth']);
        unset($data['deleted']);

        if ($new) {
            $group->setCreated(new \DateTime());
            $group->setGroupId(Uuid::uuid1());

            $data['group_id'] = $group->getGroupId();
            $data['created']  = $group->getCreated()->format(\DateTime::ISO8601);

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
     *
     * @return GroupInterface
     * @throws NotFoundException
     */
    public function fetchGroup($groupId)
    {
        $rowSet = $this->groupTableGateway->select(['group_id' => $groupId]);
        $row    = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("Group not Found");
        }

        return new Group($row->getArrayCopy());
    }

    /**
     * Fetches on group from the DB by using the external id
     *
     * @param $organization
     * @param $externalId
     *
     * @return GroupInterface
     * @throws NotFoundException
     */
    public function fetchGroupByExternalId($organization, $externalId)
    {
        $orgId  = $organization instanceof OrganizationInterface ? $organization->getOrgId() : $organization;
        $rowSet = $this->groupTableGateway->select(['organization_id' => $orgId, 'external_id' => $externalId]);
        $row    = $rowSet->current();
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
     *
     * @return bool
     */
    public function deleteGroup(GroupInterface $group, $soft = true)
    {
        $this->fetchGroup($group->getGroupId());

        if ($soft) {
            $group->setDeleted(new \DateTime());

            $this->groupTableGateway->update(
                ['deleted' => $group->getDeleted()->format(\DateTime::ISO8601)],
                ['group_id' => $group->getGroupId()]
            );

            return true;
        }

        $this->groupTableGateway->delete(['group_id' => $group->getGroupId()]);

        return true;
    }

    /**
     * Fetches all the types of groups for the children
     *
     * Used for hal link building
     *
     * @param GroupInterface $group
     *
     * @return string[]
     * @deprecated
     */
    public function fetchChildTypes(GroupInterface $group)
    {
        if (!$group->hasChildren()) {
            return [];
        }

        $select = new Select();
        $select->columns([new Expression('DISTINCT(type) AS type')]);
        $select->from(['g' => $this->groupTableGateway->getTable()]);
        $where = new Where();

        $where->addPredicate(new Operator('organization_id', '=', $group->getOrganizationId()));
        $where->addPredicate(new Between('head', ($group->getHead() + 1), ($group->getTail() - 1)));

        $select->where($where);
        $select->order(['g.title']);

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
     *
     * @return DbSelect
     */
    public function fetchChildGroups(GroupInterface $group, $where = null, $prototype = null)
    {
        $where  = $this->createWhere($where);
        $select = new Select();
        $select->from($this->groupTableGateway->getTable());

        $where->addPredicate(new Operator('organization_id', '=', $group->getOrganizationId()));
        $where->addPredicate(new Between('head', ($group->getHead() + 1), ($group->getTail() - 1)));
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
