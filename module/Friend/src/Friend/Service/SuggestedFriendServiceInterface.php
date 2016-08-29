<?php

namespace Friend\Service;

/**
 * Interface SuggestedFriendServiceInterface
 */
interface SuggestedFriendServiceInterface
{
    /**
     * Fetches the suggested users for a user
     *
     * SELECT
     *   CASE WHEN u.user_id = uf.friend_id
     *      THEN uf.status
     *   WHEN u.user_id = uf.user_id
     *       THEN uf.status
     *   ELSE 'NOT_FRIENDS' END AS 'friend_status',
     *   u.*
     * FROM user_groups AS ug
     *   LEFT JOIN groups AS ugg ON ugg.group_id = ug.group_id
     *   LEFT JOIN groups AS sg ON sg.organization_id = ugg.organization_id
     *      AND sg.head BETWEEN ugg.head AND ugg.tail
     *   LEFT OUTER JOIN user_groups AS oug ON oug.group_id = sg.group_id OR oug.group_id = ugg.parent_id
     *   LEFT OUTER JOIN user_friends AS uf ON uf.user_id = :user_id OR uf.friend_id = :user_id
     *   LEFT OUTER JOIN users AS u ON u.user_id = oug.user_id
     *      OR u.user_id = uf.friend_id
     *      OR u.user_id = uf.user_id
     * WHERE u.deleted IS NULL
     *  AND ug.user_id = :user_id
     * GROUP BY u.user_id
     * HAVING u.user_id != :user_id AND friend_status = 'NOT_FRIENDS'
     * ORDER BY u.first_name ASC, u.last_name ASC;
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     *
     * @return \Zend\Paginator\Adapter\DbSelect
     */
    public function fetchSuggestedFriends($user, $where = null, $prototype = null);
}
