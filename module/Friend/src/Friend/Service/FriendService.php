<?php

namespace Friend\Service;

use Application\Utils\ServiceTrait;
use Friend\Friend;
use Friend\FriendInterface;
use Friend\NotFriendsException;
use User\UserHydrator;
use User\UserInterface;
use Zend\Db\ResultSet\AbstractResultSet;
use Zend\Db\ResultSet\HydratingResultSet;
use \Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class FriendService
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @inheritdoc
     */
    public function fetchFriendsForUser($user, $where = null, $prototype = null) : AdapterInterface
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $where  = $this->createWhere($where);

        $select = new Select(['uf' => 'user_friends']);
        $select->columns(['uf_user_id' => 'user_id', 'uf_friend_id' => 'friend_id', 'friend_status' => 'status']);
        $select->join(
            ['u' => 'users'],
            new Expression('u.user_id = uf.friend_id OR u.user_id = uf.user_id'),
            ['*'],
            Select::JOIN_LEFT
        );

        $firstOr = new PredicateSet();
        $firstOr->orPredicate(new Operator('uf.friend_id', '=', $userId));
        $firstOr->orPredicate(new Operator('uf.user_id', '=', $userId));

        $where->addPredicate($firstOr);
        $where->addPredicate(new Operator('u.user_id', '!=', $userId));

        $select->where($where);
        $select->order(['u.first_name', 'u.last_name']);

        $hydrator  = $prototype instanceof UserInterface ? new ArraySerializable() : new UserHydrator();
        $resultSet = new HydratingResultSet($hydrator, $prototype);

        return new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function attachFriendToUser($user, $friend) : FriendInterface
    {
        $userId   = $user instanceof UserInterface ? $user->getUserId() : $user;
        $friendId = $friend instanceof UserInterface ? $friend->getUserId() : $friend;

        try {
            /** @var \ArrayObject $currentStatus */
            $currentStatus = $this->fetchFriendForUser($user, $friend, new \ArrayObject());
        } catch (NotFriendsException $notFriends) {
            $this->tableGateway->insert([
                'user_id'   => $userId,
                'friend_id' => $friendId,
                'status'    => FriendInterface::PENDING,
            ]);


            return $this->fetchFriendForUser($user, $friend, new Friend());
        }

        $isAccepting = $userId == $currentStatus['uf_friend_id'];

        if ($isAccepting && $currentStatus['friend_status'] === FriendInterface::PENDING) {
            $where = [
                'user_id'   => $currentStatus['uf_user_id'],
                'friend_id' => $currentStatus['uf_friend_id'],
                'status'    => $currentStatus['friend_status'],
            ];

            $currentStatus['friend_status'] = FriendInterface::FRIEND;

            $this->tableGateway->update(
                ['status' => FriendInterface::FRIEND],
                $where
            );
        }

        return new Friend($currentStatus->getArrayCopy());
    }

    /**
     * @inheritdoc
     */
    public function detachFriendFromUser($user, $friend) : bool
    {
        try {
            /** @var \ArrayObject $currentStatus */
            $currentStatus = $this->fetchFriendForUser($user, $friend, new \ArrayObject());
            $where         = [
                'user_id'   => $currentStatus['uf_user_id'],
                'friend_id' => $currentStatus['uf_friend_id'],
                'status'    => $currentStatus['friend_status'],
            ];
            $this->tableGateway->delete($where);
        } catch (NotFriendsException $notFriends) {
            // no op the users are not friends
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function fetchFriendForUser($user, $friend, $prototype = null, $status = null)
    {
        $select    = $this->createSelectForFriendsList($user, $friend);
        $prototype = null === $prototype ? new \ArrayObject() : $prototype;
        $hydrator  = new ArraySerializable();
        /** @var \Iterator|\Countable $results */
        $results = $this->tableGateway->selectWith($select);

        if (count($results) < 1) {
            throw new NotFriendsException();
        }

        $results->rewind();
        $row = $results->current();

        return $hydrator->hydrate($row->getArrayCopy(), $prototype);
    }

    /**
     * @inheritdoc
     */
    public function fetchFriendStatusForUser(UserInterface $user, UserInterface $friend) : string
    {
        $userId   = $user instanceof UserInterface ? $user->getUserId() : $user;
        $friendId = $friend instanceof UserInterface ? $friend->getUserId() : $friend;

        $select = new Select(['uf' => 'user_friends']);
        $select->columns(['friend_status' => 'status', 'requesting' => 'user_id']);

        $where = $this->createWhere([]);

        $firstOr = new PredicateSet();
        $firstOr->orPredicate(new Operator('uf.friend_id', '=', $userId));
        $firstOr->orPredicate(new Operator('uf.user_id', '=', $userId));

        $secondOr = new PredicateSet();
        $secondOr->orPredicate(new Operator('uf.friend_id', '=', $friendId));
        $secondOr->orPredicate(new Operator('uf.user_id', '=', $friendId));

        $where->addPredicate($firstOr);
        $where->addPredicate($secondOr);
        $select->where($where);

        /** @var AbstractResultSet $results */
        $results = $this->tableGateway->selectWith($select);
        $results->rewind();
        $row = $results->current();
        // will only have friend or pending in the DB
        switch ($row['friend_status']) {
            case FriendInterface::FRIEND:
                return FriendInterface::FRIEND;

            case FriendInterface::PENDING:
                return $user->getUserId() == $row['requesting']
                    ? FriendInterface::PENDING
                    : FriendInterface::REQUESTED;
        }

        throw new NotFriendsException();
    }

    /**
     * Creates the select statement for fetching friends
     *
     * @param UserInterface|string $user
     * @param UserInterface|string $friend
     *
     * @return Select
     */
    protected function createSelectForFriendsList($user, $friend)
    {
        $userId   = $user instanceof UserInterface ? $user->getUserId() : $user;
        $friendId = $friend instanceof UserInterface ? $friend->getUserId() : $friend;

        $select = new Select(['uf' => 'user_friends']);
        $select->columns(['uf_user_id' => 'user_id', 'uf_friend_id' => 'friend_id', 'friend_status' => 'status']);
        $select->join(
            ['u' => 'users'],
            new Expression('u.user_id = uf.friend_id OR u.user_id = uf.user_id'),
            ['*'],
            Select::JOIN_LEFT
        );

        $where = $this->createWhere([]);

        $firstOr = new PredicateSet();
        $firstOr->orPredicate(new Operator('uf.friend_id', '=', $userId));
        $firstOr->orPredicate(new Operator('uf.user_id', '=', $userId));

        $secondOr = new PredicateSet();
        $secondOr->orPredicate(new Operator('uf.friend_id', '=', $friendId));
        $secondOr->orPredicate(new Operator('uf.user_id', '=', $friendId));

        $where->addPredicate($firstOr);
        $where->addPredicate($secondOr);
        $where->addPredicate(new Operator('u.user_id', '!=', $userId));
        $select->where($where);
        $select->order(['u.first_name', 'u.last_name']);

        return $select;
    }
}
