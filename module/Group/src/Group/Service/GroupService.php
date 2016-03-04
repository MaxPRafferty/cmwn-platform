<?php

namespace Group\Service;

use Application\Exception\NotFoundException;
use Group\Group;
use Ramsey\Uuid\Uuid;
use Group\GroupInterface;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Select;
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
    /**
     * @var TableGateway
     */
    protected $groupTableGateway;

    public function __construct(TableGateway $gateway)
    {
        $this->groupTableGateway = $gateway;
    }

    /**
     * Attaches a user to a group
     *
     * @param GroupInterface $group
     * @param UserInterface $user
     * @param $role
     * @return bool
     */
    public function attachUserToGroup(GroupInterface $group, UserInterface $user, $role)
    {
        // TODO: Implement attachUserToGroup() method.
    }

    /**
     * Detaches a user from a group
     *
     * @param GroupInterface $group
     * @param UserInterface $user
     * @return bool
     */
    public function detachUserFromGroup(GroupInterface $group, UserInterface $user)
    {
        // TODO: Implement detachUserFromGroup() method.
    }

    /**
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where     = !$where instanceof PredicateInterface ? new Where($where) : $where;
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
}
