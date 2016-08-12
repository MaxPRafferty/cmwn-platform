<?php

namespace Security\Service;

use User\Service\UserServiceInterface;
use User\User;
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
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * GroupService constructor.
     *
     * @param TableGateway $pivotTable
     */
    public function __construct(TableGateway $pivotTable, UserServiceInterface $userService)
    {
        $this->pivotTable  = $pivotTable;
        $this->userService = $userService;
    }

    /**
     * @inheritdoc
     */
    public function fetchRelationshipRole(UserInterface $activeUser, $requestedUser)
    {
        // get the actual user
        if (!$requestedUser instanceof UserInterface) {
            $requestedUser = $this->userService->fetchUser($requestedUser);
        }

        // Same user FTW!
        if ($activeUser->getUserId() === $requestedUser->getUserId()) {
            return 'me.' . strtolower($activeUser->getType());
        }

        $this->activeUser    = $activeUser;
        $this->requestedUser = $requestedUser;

        return $this->getRoleFromDb();
    }

    /**
     * @return bool|string
     */
    protected function getRoleFromDb()
    {
        $activeUserId    = $this->activeUser->getUserId();
        $requestedUserId = $this->requestedUser->getUserId();

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
                'active_tail' => 'tail',
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
                'requested_head'  => 'head',
                'requested_tail'  => 'tail',
                'requested_group' => 'group_id',
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
            'active_group.network_id',
            '=',
            new Expression('requested_group.network_id')
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
     *
     * @return string
     */
    protected function marshalRole(\ArrayObject $row)
    {
        // Give us something nice to pass around
        $result = new \stdClass();

        $result->active_role         = $row->active_role;
        $result->active_head         = (int)$row->active_head;
        $result->active_tail         = (int)$row->active_tail;
        $result->active_parent       = new \stdClass();
        $result->active_parent->head = (int)$row->active_parent_head;
        $result->active_parent->tail = (int)$row->active_parent_tail;

        $result->requested_head         = (int)$row->requested_head;
        $result->requested_tail         = (int)$row->requested_tail;
        $result->requested_parent       = new \stdClass();
        $result->requested_parent->head = (int)$row->requested_parent_head;
        $result->requested_parent->tail = (int)$row->requested_parent_tail;

        return $this->isActiveSameTypeAsRequested()
            ? $this->marshalRoleForSameTypes($result)
            : $this->marshalRoleForDifferentTypes($result);
    }

    /**
     * Different cases when the two users are different
     *
     * @param \stdClass $row
     *
     * @return string
     */
    protected function marshalRoleForDifferentTypes(\stdClass $row)
    {
        // Active and requested are in the same group
        if ($this->isSameNode($row)) {
            return $row->active_role . '.' . strtolower($this->activeUser->getType());
        }

        // requested group is child of active group
        if ($this->isRequestedInChildNodeOfActive($row)) {
            return $row->active_role . '.' . strtolower($this->activeUser->getType());
        }

        if ($this->activeUser->getType() === UserInterface::TYPE_ADULT) {
            return 'guest';
        }

        return $this->isActiveInChildNodeOfRequested($row) ? $row->active_role . '.child' : 'guest';
    }

    /**
     * Figures out the role if the 2 users are the same type
     *
     * @param \stdClass $row
     *
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
            return $row->active_role . '.child';
        }

        // Siblings
        if ($row->active_parent->head === $row->requested_parent->head) {
            return 'neighbor.adult';
        }

        // if the requested is below the active, return the role
        if ($row->active_tail > $row->requested_tail) {
            return $row->active_role . '.adult';
        }

        return 'neighbor.adult';
    }

    protected function findRequestedInTree(\stdClass $row)
    {
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
     *
     * @return bool
     */
    protected function isRequestedInSameNetworkAsActive(\stdClass $row)
    {
        return ($row->requested_head > $row->active_head) && ($row->requested_tail < $row->active_tail);
    }

    /**
     * @param \stdClass $row
     *
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
     *
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
     *
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
     *
     * @return bool
     */
    protected function isRequestedInChildNodeOfActive(\stdClass $row)
    {
        return ($row->requested_head > $row->active_head) && ($row->requested_tail < $row->active_tail);
    }
}
