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
        $activeUserId        = $activeUser->getUserId();
        $requestedUserId     = $requestedUser->getUserId();

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
            ['active_head' => 'head', 'active_tail' => 'tail'],
            Select::JOIN_LEFT
        );

        // join the active parent group
        $select->join(
            ['active_parent_group' => 'groups'],
            'active_parent_group.group_id = active_group.parent_id',
            ['active_parent_head' => 'head', 'active_parent_tail' => 'tail'],
            Select::JOIN_LEFT
        );

        // join the requested group
        $select->join(
            ['requested_group' => 'groups'],
            'requested_user.group_id = requested_group.group_id',
            ['requested_head' => 'head', 'requested_tail' => 'tail'],
            Select::JOIN_LEFT
        );

        // join the requested parent group
        $select->join(
            ['requested_parent_group' => 'groups'],
            'requested_parent_group.group_id = requested_group.parent_id',
            ['requested_parent_head' => 'head', 'requested_parent_tail' => 'tail'],
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

    protected function marshalRole(\ArrayObject $row)
    {
        return $this->isSameType() ? $this->marshalRoleForSameTypes($row) : $this->marshalRoleForDifferentTypes($row);
    }

    /**
     * @return bool
     */
    protected function isSameType()
    {
        return $this->activeUser->getType() === $this->requestedUser->getType();
    }

    protected function marshalRoleForDifferentTypes(\ArrayObject $row)
    {
        // Active and requested are in the same group
        if ($this->isSameNode($row)) {
            return $row->active_role;
        }

        // requested group is child of active group
        if ($row->requested_head > $row->active_head && $row->requested_tail < $row->active_tail) {
            return $row->active_role;
        }

        if ($this->activeUser->getType() !== UserInterface::TYPE_CHILD) {
            return 'guest';
        }

        // if the users are outside the network, then it is guest
        if (!$this->isInSameNetwork($row)) {
            return 'guest';
        }

        return $row->active_role;
    }

    protected function marshalRoleForSameTypes(\ArrayObject $row)
    {
        $networkHead = $row->active_parent_head === null ? (int) $row->active_head : (int) $row->active_parent_head;
        $networkTail = $row->active_parent_tail === null ? (int) $row->active_tail : (int) $row->active_parent_tail;

        // if the users are outside the network, then it is guest
        if (($row->requested_head < $networkHead) && ($row->requested_head > $networkTail)) {
            return 'guest';
        }

        if ($this->activeUser->getType() === UserInterface::TYPE_ADULT) {
            return 'neighbor.adult';
        }

        return $row->active_role;
    }

    protected function isInSameNetwork(\ArrayObject $row)
    {
        $networkHead = $row->active_parent_head === null ? (int) $row->active_head : (int) $row->active_parent_head;
        $networkTail = $row->active_parent_tail === null ? (int) $row->active_tail : (int) $row->active_parent_tail;

        $nodeHead    = (int) $row->active_head;
        $nodeTail    = (int) $row->active_tail;

        return ($nodeHead > $networkHead) && ($nodeTail < $networkTail);
    }

    protected function isSameNode(\ArrayObject $row)
    {
        $requestedHead = (int) $row->requested_head;
        $requestedTail = (int) $row->requested_tail;

        $activeHead    = (int) $row->active_head;
        $activeTail    = (int) $row->active_tail;

        return ($activeHead === $requestedHead) && ($activeTail === $requestedTail);
    }
}
