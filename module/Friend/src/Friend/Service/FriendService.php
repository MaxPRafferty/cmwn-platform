<?php

namespace Friend\Service;

use Application\Utils\ServiceTrait;
use Friend\NotFriendsException;
use User\UserHydrator;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use \Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class FriendService
 */
class FriendService implements FriendServiceInterface
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
            ['uf' => 'user_friends'],
            new Expression('uf.user_id = ? OR uf.friend_id = ?', [$userId, $userId]),
            ['friend_id' => 'friend_id'],
            Select::JOIN_LEFT
        );

        $where->addPredicate(new Expression('u.user_id = uf.user_id'));
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

    /**
     * Fetches a friend for a user
     *
     * SELECT
     *   u.*,
     *   uf.friend_id AS user_friend_id
     * FROM user_friends AS uf
     *   LEFT JOIN users AS u ON u.user_id = uf.user_id
     * WHERE (uf.friend_id = :friend_id OR uf.user_id = :friend_id)
     *   AND (uf.user_id = :user_id OR uf.friend_id = :user_id);
     *
     * @param $user
     * @param $friend
     * @param null $prototype
     * @throws NotFriendsException
     * @return object|UserInterface
     */
    public function fetchFriendForUser($user, $friend, $prototype = null)
    {
        $userId   = $user instanceof UserInterface ? $user->getUserId() : $user;
        $friendId = $friend instanceof UserInterface ? $friend->getUserId() : $friend;

        $select = new Select(['uf' => 'user_friends']);
        $select->columns(['user_friend_id' => 'friend_id']);
        $select->join(
            ['u' => 'users'],
            new Expression('u.user_id = uf.user_id'),
            ['*'],
            Select::JOIN_LEFT
        );

        $where = $this->createWhere([]);

        $firstOr = new PredicateSet();
        $firstOr->orPredicate(new Operator('uf.friend_id', '=', $friendId));
        $firstOr->orPredicate(new Operator('uf.user_id', '=', $friendId));

        $secondOr = new PredicateSet();
        $secondOr->orPredicate(new Operator('uf.friend_id', '=', $userId));
        $secondOr->orPredicate(new Operator('uf.user_id', '=', $userId));

        $where->addPredicate($firstOr);
        $where->addPredicate($secondOr);
        $select->where($where);

        $hydrator  = $prototype instanceof UserInterface ? new ArraySerializable() : new UserHydrator();
        /** @var \Iterator|\Countable $results */
        $results   = $this->tableGateway->selectWith($select);

        if ($results->count() < 1) {
            throw new NotFriendsException();
        }

        $results->rewind();
        $row = $results->current();
        return $hydrator->hydrate($row->getArrayCopy(), $prototype);
    }
}
