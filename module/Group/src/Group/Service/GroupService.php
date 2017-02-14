<?php

namespace Group\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Group\Group;
use Group\GroupInterface;
use Zend\Db\Adapter\Driver\Pdo\Connection;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Between;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Json\Json;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Group Service that saves data to the database
 */
class GroupService implements GroupServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $groupTableGateway;

    /**
     * @var ArraySerializable
     */
    protected $hydrator;


    /**
     * GroupService constructor.
     *
     * @param TableGateway $gateway
     */
    public function __construct(TableGateway $gateway)
    {
        $this->groupTableGateway = $gateway;
        $this->hydrator          = new ArraySerializable();
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
    public function attachChildToGroup(GroupInterface $parent, GroupInterface $child): bool
    {
        $child->attachToGroup($parent);

        // fetch the parent to get the latest head value
        $parent->exchangeArray($this->fetchGroup($parent->getGroupId())->getArrayCopy());

        // if the head for the parent is 0, then both are not in the tree so make them a tree
        if ($parent->getHead() < 1) {
            $this->groupTableGateway->update(
                ['head' => 1, 'tail' => 4],
                ['group_id' => $parent->getGroupId()]
            );

            $this->groupTableGateway->update(
                ['head' => 2, 'tail' => 3, 'network_id' => $parent->getNetworkId()],
                ['group_id' => $child->getGroupId()]
            );

            return true;
        }

        /** @var Connection $connection */
        $connection = $this->groupTableGateway->getAdapter()->getDriver()->getConnection();
        $connection->beginTransaction();

        try {
            // Shift the tail
            $where = new Where();
            $where->addPredicate(new Operator('tail', '>', $parent->getHead()));
            $where->addPredicate(new Operator('network_id', '=', $parent->getNetworkId()));

            $this->groupTableGateway->update(
                ['tail' => new Expression('tail + 2')],
                $where
            );

            // Shift the head
            $where = new Where();
            $where->addPredicate(new Operator('head', '>', $parent->getHead()));
            $where->addPredicate(new Operator('network_id', '=', $parent->getNetworkId()));
            $this->groupTableGateway->update(
                ['head' => new Expression('head + 2')],
                $where
            );

            // Set the child
            $where = new Where();
            $where->addPredicate(new Operator('group_id', '=', $child->getGroupId()));
            $this->groupTableGateway->update(
                [
                    'head'       => $parent->getHead() + 1,
                    'tail'       => $parent->getHead() + 2,
                    'network_id' => $child->getNetworkId(),
                    'parent_id'  => $parent->getGroupId(),
                ],
                $where
            );

            $connection->commit();
        } catch (\Exception $attachException) {
            $connection->rollback();
            throw $attachException;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, GroupInterface $prototype = null): AdapterInterface
    {
        $where     = $this->createWhere($where);
        $prototype = $prototype ?? new Group();
        $resultSet = new HydratingResultSet($this->hydrator, $prototype);

        $select = new Select(['g' => $this->groupTableGateway->getTable()]);
        $select->where($where);
        $select->order(['g.title']);

        return new DbSelect(
            $select,
            $this->groupTableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function createGroup(GroupInterface $group): bool
    {
        $group->setCreated(new \DateTime());
        $group->setUpdated(new \DateTime());
        $data         = $group->getArrayCopy();
        $data['meta'] = Json::encode($data['meta']);

        unset($data['depth']);
        unset($data['deleted']);
        unset($data['organization']); // TODO Remove when ZF-Hal is respecting entities that are link collection aware
        unset($data['parent']); // TODO Remove when ZF-Hal is respecting entities that are link collection aware

        $data['group_id'] = $group->getGroupId();
        $data['created']  = $group->getCreated()->format(\DateTime::ISO8601);

        $this->groupTableGateway->insert($data);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function updateGroup(GroupInterface $group): bool
    {
        $group->setUpdated(new \DateTime());
        $data         = $group->getArrayCopy();
        $data['meta'] = Json::encode($data['meta']);
        unset($data['depth']);
        unset($data['deleted']);
        unset($data['organization']); // TODO Remove when ZF-Hal is respecting entities that are link collection aware
        unset($data['parent']); // TODO Remove when ZF-Hal is respecting entities that are link collection aware

        $this->fetchGroup($group->getGroupId());

        $this->groupTableGateway->update(
            $data,
            ['group_id' => $group->getGroupId()]
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function fetchGroup(string $groupId, GroupInterface $prototype = null): GroupInterface
    {
        $rowSet = $this->groupTableGateway->select(['group_id' => $groupId]);
        $row    = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("Group not Found");
        }

        $prototype = $prototype ?? new Group();
        $this->hydrator->hydrate($row->getArrayCopy(), $prototype);

        return $prototype;
    }

    /**
     * @inheritdoc
     */
    public function fetchGroupByExternalId(
        string $networkId,
        string $externalId,
        GroupInterface $prototype = null
    ): GroupInterface {
        $rowSet = $this->groupTableGateway->select(['network_id' => $networkId, 'external_id' => $externalId]);
        $row    = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("Group not Found");
        }

        $prototype = $prototype ?? new Group();
        $this->hydrator->hydrate($row->getArrayCopy(), $prototype);

        return $prototype;
    }

    /**
     * @inheritdoc
     */
    public function deleteGroup(GroupInterface $group, bool $soft = true): bool
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
     * @inheritdoc
     */
    public function fetchChildTypes(GroupInterface $group): array
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
     * @inheritdoc
     */
    public function fetchChildGroups(
        GroupInterface $group,
        $where = null,
        GroupInterface $prototype = null
    ): AdapterInterface {
        $where  = $this->createWhere($where);
        $select = new Select();
        $select->from(['g' => $this->groupTableGateway->getTable()]);

        $where->addPredicate(new Operator('g.network_id', '=', $group->getNetworkId()));
        $where->addPredicate(new Between('g.head', ($group->getHead() + 1), ($group->getTail() - 1)));
        $select->where($where);

        $prototype = $prototype ?? new Group();
        $resultSet = new HydratingResultSet($this->hydrator, $prototype);

        return new DbSelect(
            $select,
            $this->groupTableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function fetchGroupTypes(): array
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
