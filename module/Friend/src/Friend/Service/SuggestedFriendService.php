<?php

namespace Friend\Service;

use Application\Utils\ServiceTrait;
use Group\Service\UserGroupServiceInterface;
use User\UserHydrator;
use User\UserInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Expression;
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
     * Fetches the suggested users for a user
     *
     * SELECT
     *  CASE WHEN u.user_id = uf.friend_id THEN uf.status
     *      WHEN u.user_id = uf.user_id THEN uf.status ELSE 'NOT_FRIENDS' END AS 'friend_status',
     *  `u`.*
     * FROM `user_groups` AS `ug`
     *  LEFT JOIN `groups` AS `ugg` ON `ugg`.`group_id` = `ug`.`group_id`
     *  LEFT JOIN `groups` AS `sg` ON `sg`.`organization_id` = `ugg`.`organization_id`
     *      AND `sg`.`head` BETWEEN `ugg`.`head` AND `ugg`.`tail`
     *  LEFT JOIN `groups` AS `g` ON `g`.`group_id` = `sg`.`group_id` OR `g`.`group_id` = `ugg`.`parent_id`
     *  LEFT OUTER JOIN `user_groups` AS `oug` ON `oug`.`group_id` = `g`.`group_id`
     *  LEFT  JOIN `user_friends` AS `uf` ON `uf`.`user_id` = 'english_student'
     *  LEFT OUTER JOIN `users` AS `u` ON `u`.`user_id` = `oug`.`user_id`
     *      OR `u`.`user_id` = `uf`.`friend_id`
     *      OR `u`.`user_id` = `uf`.`user_id`
     * WHERE  `u`.`deleted` IS NULL
     *  AND `ug`.`user_id` = 'english_student'
     * GROUP BY u.user_id
     * HAVING u.user_id != 'english_student' AND friend_status = 'NOT_FRIENDS'
     * ORDER BY `u`.`first_name` ASC, `u`.`last_name` ASC;
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     *
     * @return \Zend\Paginator\Adapter\DbSelect
     */
    public function fetchSuggestedFriends($user, $where = null, $prototype = null)
    {
        $case = new Expression(
            'CASE WHEN u.user_id = uf.friend_id THEN uf.status 
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
            'sg.organization_id = ugg.organization_id AND sg.head BETWEEN ugg.head AND ugg.tail',
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
        $select->having(new Operator('friend_status', '=', 'NOT_FRIENDS'));
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
