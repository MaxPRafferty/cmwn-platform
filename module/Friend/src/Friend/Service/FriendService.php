<?php

namespace Friend\Service;

use Application\Utils\ServiceTrait;
use User\UserHydrator;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class FriendService
 */
class FriendService
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * FriendService constructor.
     *
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Fetches all the friends for a user
     *
     * @param string|UserInterface $user
     * @param null|array|PredicateInterface $where
     * @param null|UserInterface|object $prototype
     * @return DbSelect
     */
    public function fetchFriendsForUser($user, $where = null, $prototype = null)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $where  = $this->createWhere($where);

        $select = new Select(['u' => 'users']);
        $select->join(
            ['friends' => 'user_friends'],
            new Expression('friends.user_id = ?', $userId),
            ['friend_id' => 'friend_id'],
            Select::JOIN_LEFT
        );

        $select->where($where);
        $hydrator  = $prototype instanceof UserInterface ? new ArraySerializable() : new UserHydrator();
        $resultSet = new HydratingResultSet($hydrator, $prototype);
        return new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * Adds a friend to a user
     *
     * @param string|UserInterface $user
     * @param string|UserInterface $friend
     * @return bool
     */
    public function attachFriendToUser($user, $friend)
    {
        $userId   = $user instanceof UserInterface ? $user->getUserId() : $user;
        $friendId = $friend instanceof UserInterface ? $friend->getUserId() : $friend;

        $this->tableGateway->insert(['user_id' => $userId, 'friend_id' => $friendId]);
        return true;
    }

    /**
     * Removes a friend from a user
     *
     * @param string|UserInterface $user
     * @param string|UserInterface $friend
     * @return bool
     */
    public function detachFriendFromUser($user, $friend)
    {
        $userId   = $user instanceof UserInterface ? $user->getUserId() : $user;
        $friendId = $friend instanceof UserInterface ? $friend->getUserId() : $friend;

        $this->tableGateway->delete(['user_id' => $userId, 'friend_id' => $friendId]);
        return true;
    }
}
