<?php

namespace Security\Service;

use User\UserInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * Class SecurityGroupService
 */
class SecurityGroupService implements SecurityGroupServiceInterface
{
    /**
     * @var TableGateway
     */
    protected $pivotTable;

    /**
     * @var UserInterface
     */
    protected $activeUser;

    /**
     * @var UserInterface
     */
    protected $requestedUser;

    /**
     * GroupService constructor.
     * @param TableGateway $pivotTable
     */
    public function __construct(TableGateway $pivotTable)
    {
        $this->pivotTable = $pivotTable;
    }

    /**
     * Finds the role the user has to another user
     *
     * SELECT
     *  requested_user.user_id AS requested_user_id,
     *
     *  active_user.role AS active_role,
     *
     *  active_group.head AS active_head,
     *  active_group.tail AS active_tail,
     *
     *  active_parent_group.head AS active_parent_head,
     *  active_parent_group.tail AS active_parent_tail,
     *  active_parent_group.group_id AS active_parent_group,
     *
     *  requested_group.head AS requested_head,
     *  requested_group.tail AS requested_tail,
     *  requested_group.group_id AS requested_group,
     *
     *  requested_parent_group.head AS requested_parent_head,
     *  requested_parent_group.tail AS requested_parent_tail,
     *  requested_parent_group.group_id AS requested_parent_group
     *
     * FROM user_groups AS active_user
     *  LEFT JOIN user_groups AS requested_user ON requested_user.user_id = '974263e8-2806-11e6-af9a-b1fd6b0b32b7'
     *  LEFT JOIN groups AS active_group ON active_group.group_id = requested_user.group_id
     *  LEFT JOIN groups AS active_parent_group ON active_parent_group.group_id = active_group.parent_id
     *  LEFT JOIN groups AS requested_group ON requested_group.group_id = requested_user.group_id
     *  LEFT JOIN groups AS requested_parent_group ON requested_parent_group.group_id = requested_group.parent_id
     *
     * WHERE active_user.user_id = '964437d2-2806-11e6-b002-8eb532838af7'
     *  AND active_group.organization_id = requested_group.organization_id
     *
     *
     * @param UserInterface $activeUser
     * @param UserInterface $requestedUser
     * @return string
     */
    public function fetchRelationshipRole(UserInterface $activeUser, UserInterface $requestedUser)
    {
        // Same user FTW!
        // ..... Wow!
        // ... Cool! .... such fun
        // . Awesome
        if ($activeUser->getUserId() === $requestedUser->getUserId()) {
            return 'me';
        }

        $this->activeUser    = $activeUser;
        $this->requestedUser = $requestedUser;

        $activeUserId    = $activeUser->getUserId();
        $requestedUserId = $requestedUser->getUserId();

        $select = new Select();
        $select->columns(['active_role' => 'active_user.role'], false);
        $select->from(['active_user' => 'user_groups']);

        // Join the requested user group
        $select->join(
            ['requested_user' => 'user_groups'],
            new Expression('requested_user.user_id = ?', $requestedUserId, []), // FIXME @ ZF 3.0+
            ['requested_user_id' => 'user_id'],
            Select::JOIN_LEFT
        );

        // join the active group
        $select->join(
            ['active_group' => 'groups'],
            'active_user.group_id = active_group.group_id',
            [
                'active_head' => 'head',
                'active_tail' => 'tail'
            ],
            Select::JOIN_LEFT
        );

        // join the active parent group
        $select->join(
            ['active_parent_group' => 'groups'],
            'active_parent_group.group_id = active_group.parent_id',
            [
                'active_parent_head'  => 'head',
                'active_parent_tail'  => 'tail',
                'active_parent_group' => 'group_id',
            ],
            Select::JOIN_LEFT
        );

        // join the requested group
        $select->join(
            ['requested_group' => 'groups'],
            'requested_user.group_id = requested_group.group_id',
            [
                'requested_head' => 'head',
                'requested_tail' => 'tail',
                'requested_group' => 'group_id'
            ],
            Select::JOIN_LEFT
        );

        // join the requested parent group
        $select->join(
            ['requested_parent_group' => 'groups'],
            'requested_parent_group.group_id = requested_group.parent_id',
            [
                'requested_parent_head'  => 'head',
                'requested_parent_tail'  => 'tail',
                'requested_parent_group' => 'group_id',
            ],
            Select::JOIN_LEFT
        );

        $where = new PredicateSet();
        $where->addPredicate(new Operator(
            'active_user.user_id',
            '=',
            $activeUserId
        ));

        $where->addPredicate(new Operator(
            'active_group.organization_id',
            '=',
            new Expression('requested_group.organization_id')
        ));

        $select->where($where);
        $results = $this->pivotTable->selectWith($select);
        if (count($results) < 1) {
            return false;
        }

        $results->rewind();
        return $this->marshalRole($results->current());
    }

    /**
     * Figures out the comparing role for the active user
     *
     * @param \ArrayObject $row
     * @return string
     */
    protected function marshalRole(\ArrayObject $row)
    {

        $result = new \stdClass();

        $result->active_role = $row->active_role;
        $result->active_head = (int) $row->active_head;
        $result->active_tail = (int) $row->active_tail;
        $result->active_parent = new \stdClass();
        $result->active_parent->head = (int) $row->active_parent_head;
        $result->active_parent->tail = (int) $row->active_parent_tail;

        $result->requested_head = (int) $row->requested_head;
        $result->requested_tail = (int) $row->requested_tail;
        $result->requested_parent = new \stdClass();
        $result->requested_parent->head = (int) $row->requested_parent_head;
        $result->requested_parent->tail = (int) $row->requested_parent_tail;

        return $this->isActiveSameTypeAsRequested()
            ? $this->marshalRoleForSameTypes($result)
            : $this->marshalRoleForDifferentTypes($result);
    }

    /**
     * Different cases when the two users are different
     *
     * @param \stdClass $row
     * @return string
     */
    protected function marshalRoleForDifferentTypes(\stdClass $row)
    {
        // Active and requested are in the same group
        if ($this->isSameNode($row)) {
            return $row->active_role;
        }

        // requested group is child of active group
        if ($this->isRequestedInChildNodeOfActive($row)) {
            return $row->active_role;
        }

        if ($this->activeUser->getType() === UserInterface::TYPE_ADULT) {
            return 'guest';
        }

        return $this->isActiveInChildNodeOfRequested($row) ? $row->active_role : 'guest';
    }

    /**
     * Figures out the role if the 2 users are the same type
     *
     * @param \stdClass $row
     * @return string
     */
    protected function marshalRoleForSameTypes(\stdClass $row)
    {
        // if the users are outside the network, then it is guest
        if (!$this->isActiveInSameNetworkAsRequested($row)) {
            return 'guest';
        }

        // children in the same network always have the same relationship
        if ($this->activeUser->getType() === UserInterface::TYPE_CHILD) {
            return $row->active_role;
        }

        return 'neighbor.adult';
    }

    /**
     * Quick helper to check the requested and active are the same type
     *
     * @return bool
     */
    protected function isActiveSameTypeAsRequested()
    {
        return $this->activeUser->getType() === $this->requestedUser->getType();
    }

    /**
     * @param \stdClass $row
     * @return bool
     */
    protected function isRequestedInSameNetworkAsActive(\stdClass $row)
    {
        return ($row->requested_head > $row->active_head) && ($row->requested_tail < $row->active_tail);
    }

    /**
     * @param \stdClass $row
     * @return bool
     */
    protected function isActiveInSameNetworkAsRequested(\stdClass $row)
    {
        //requested is in the root group
        if ($row->requested_parent->head === 0) {
            return ($row->active_head >= $row->requested_head)
                && ($row->active_tail <= $row->requested_tail);
        }

        return ($row->active_head >= $row->requested_parent->head)
            && ($row->active_tail <= $row->requested_parent->tail);
    }

    /**
     * Checks if the two users are in the same network node
     *
     * @param \stdClass $row
     * @return bool
     */
    protected function isSameNode(\stdClass $row)
    {
        return ($row->active_head === $row->requested_head) && ($row->active_tail === $row->requested_tail);
    }

    /**
     * Checks if the active user is a child of the requested user
     *
     * @param \stdClass $row
     * @return bool
     */
    protected function isActiveInChildNodeOfRequested(\stdClass $row)
    {
        return ($row->active_head > $row->requested_head) && ($row->active_tail < $row->requested_tail);
    }

    /**
     * Checks if the active user is a child of the requested user
     *
     * @param \stdClass $row
     * @return bool
     */
    protected function isRequestedInChildNodeOfActive(\stdClass $row)
    {
        return ($row->requested_head > $row->active_head) && ($row->requested_tail < $row->active_tail);
    }
}
