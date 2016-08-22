<?php

namespace Friend\Service;

use Application\Utils\ServiceTrait;
use User\UserHydrator;
use User\UserInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\NotIn;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class SuggestedFriendService
 */
class SuggestedFriendService implements SuggestedFriendServiceInterface
{
    use ServiceTrait;

    /**
     * @var AdapterInterface
     */
    protected $dbAdapter;

    /**
     * SuggestedFriendService constructor.
     *
     * @param AdapterInterface $adapterInterface
     */
    public function __construct(AdapterInterface $adapterInterface)
    {
        $this->dbAdapter = $adapterInterface;
    }

    /**
     * @inheritdoc
     */
    public function fetchSuggestedFriends($user, $where = null, $prototype = null)
    {
        $case = new Expression(
            'CASE WHEN u.user_id = uf.friend_id THEN \'WAITING\' 
            WHEN u.user_id = uf.user_id THEN uf.status 
            ELSE \'NOT_FRIENDS\' END'
        );

        $select = new Select(['ug' => 'user_groups']);
        $select->columns([
            'friend_status' => $case,
            'ug_role' => 'role'
        ]);

        // This is the groups that $userId belongs too
        $select->join(
            ['ugg' => 'groups'],
            'ugg.group_id = ug.group_id',
            ['user_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

        // This is all the sub groups from above
        $select->join(
            ['sg' => 'groups'],
            'sg.network_id = ugg.network_id AND sg.head BETWEEN ugg.head AND ugg.tail',
            ['sub_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

        // This is all the groups
        $select->join(
            ['g' => 'groups'],
            'g.group_id = sg.group_id OR g.group_id = ugg.parent_id',
            ['real_group_id' => 'group_id'],
            Select::JOIN_LEFT
        );

        // This is all the groups other users belong too
        $select->join(
            ['oug' => 'user_groups'],
            'oug.group_id = g.group_id',
            ['other_group_id' => 'group_id'],
            Select::JOIN_LEFT_OUTER
        );

        // This includes all the friends
        $select->join(
            ['uf' => 'user_friends'],
            'uf.user_id = ug.user_id OR uf.friend_id = ug.user_id',
            ['real_friend_status' => 'status'],
            Select::JOIN_LEFT_OUTER
        );

        // Finially we come to what we really want, and that is the users
        $select->join(
            ['u' => 'users'],
            'u.user_id = oug.user_id OR u.user_id = uf.friend_id OR u.user_id = uf.user_id',
            '*',
            Select::JOIN_LEFT_OUTER
        );

        $where  = $this->createWhere($where);
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;

        $where->addPredicate(new Operator('ug.user_id', '=', $userId));
        $where->addPredicate(new Operator('u.type', '=', UserInterface::TYPE_CHILD));
        $select->where($where);
        $select->group(['u.user_id']);
        $select->having(new Operator('u.user_id', '!=', $userId));
        $select->having(new NotIn('friend_status', ['FRIENDS', 'WAITING']));
        $select->order(['u.first_name', 'u.last_name']);

        $hydrator  = $prototype instanceof UserInterface ? new ArraySerializable() : new UserHydrator();
        $resultSet = new HydratingResultSet($hydrator, $prototype);
        return new DbSelect(
            $select,
            $this->dbAdapter,
            $resultSet
        );
    }
}
